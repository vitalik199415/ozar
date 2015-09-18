<?php
require_once "./system/libraries/Cart.php";
class AG_Cart extends CI_Cart 
{
	protected $cart_key_suffix = '';

	public function __construct($params = array())
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		// Are any config settings being passed manually?  If so, set them
		$config = array();
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				$config[$key] = $val;
			}
		}

		// Load the Sessions class
		$this->CI->load->library('session', $config);

		// Grab the shopping cart array from the session table, if it exists
		if ($this->CI->session->userdata('cart_contents'.$this->cart_key_suffix) !== FALSE)
		{
			$this->_cart_contents = $this->CI->session->userdata('cart_contents'.$this->cart_key_suffix);
		}
		else
		{
			// No cart exists so we'll set some base values
			$this->_cart_contents['cart_total'] = 0;
			$this->_cart_contents['total_items'] = 0;
		}

		log_message('debug', "Cart Class Initialized");
	}

	public function set_cart_key_suffix($suffix = '')
	{
		$this->cart_key_suffix = $suffix;
		$this->__construct();
	}

	public function _save_cart()
	{
		// Unset these so our total can be calculated correctly below
		unset($this->_cart_contents['total_items']);
		unset($this->_cart_contents['cart_total']);

		// Lets add up the individual prices and set the cart sub-total
		$total = 0;
		$items = 0;
		foreach ($this->_cart_contents as $key => $val)
		{
			// We make sure the array contains the proper indexes
			if ( ! is_array($val) OR ! isset($val['price']) OR ! isset($val['qty']))
			{
				continue;
			}

			$total += ($val['price'] * $val['qty']);
			$items += $val['qty'];

			// Set the subtotal
			$this->_cart_contents[$key]['subtotal'] = ($this->_cart_contents[$key]['price'] * $this->_cart_contents[$key]['qty']);
		}

		// Set the cart total and total items.
		$this->_cart_contents['total_items'] = $items;
		$this->_cart_contents['cart_total'] = $total;

		// Is our cart empty?  If so we delete it from the session
		if (count($this->_cart_contents) <= 2)
		{
			$this->CI->session->unset_userdata('cart_contents'.$this->cart_key_suffix);

			// Nothing more to do... coffee time!
			return FALSE;
		}

		// If we made it this far it means that our cart has data.
		// Let's pass it to the Session class so it can be stored
		//$this->CI->session->set_userdata(array('cart_contents' => $this->_cart_contents));

		//AG_CHANGE
		$this->CI->session->set_userdata('cart_contents'.$this->cart_key_suffix, $this->_cart_contents);

		// Woot!
		return TRUE;
	}

	public function destroy()
	{
		unset($this->_cart_contents);

		$this->_cart_contents['cart_total'] = 0;
		$this->_cart_contents['total_items'] = 0;

		$this->CI->session->unset_userdata('cart_contents'.$this->cart_key_suffix);
	}

	public function isset_cart_item($item)
	{
		if (isset($item['options']) AND count($item['options']) > 0)
		{
			$rowid = md5($item['id'].implode('', $item['options']));
		}
		else
		{
			$rowid = md5($item['id']);
		}
		if(isset($this->_cart_contents[$rowid]))
		{
			return $this->_cart_contents[$rowid];
		}
		return FALSE;
	}
	/*
	public function _insert($items = array())
	{
		// Was any cart data passed? No? Bah...
		if ( ! is_array($items) OR count($items) == 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Does the $items array contain an id, quantity, price, and name?  These are required
		if ( ! isset($items['id']) OR ! isset($items['qty']) OR ! isset($items['price']) OR ! isset($items['name']))
		{
			log_message('error', 'The cart array must contain a product ID, quantity, price, and name.');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Prep the quantity. It can only be a number.  Duh...
		$items['qty'] = trim(preg_replace('/([^0-9])/i', '', $items['qty']));
		// Trim any leading zeros
		$items['qty'] = trim(preg_replace('/(^[0]+)/i', '', $items['qty']));

		// If the quantity is zero or blank there's nothing for us to do
		if ( ! is_numeric($items['qty']) OR $items['qty'] == 0)
		{
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Validate the product ID. It can only be alpha-numeric, dashes, underscores or periods
		// Not totally sure we should impose this rule, but it seems prudent to standardize IDs.
		// Note: These can be user-specified by setting the $this->product_id_rules variable.
		if ( ! preg_match("/^[".$this->product_id_rules."]+$/i", $items['id']))
		{
			log_message('error', 'Invalid product ID.  The product ID can only contain alpha-numeric characters, dashes, and underscores');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Validate the product name. It can only be alpha-numeric, dashes, underscores, colons or periods.
		// Note: These can be user-specified by setting the $this->product_name_rules variable.
		if ( ! preg_match("/^[".$this->product_name_rules."]+$/i", $items['name']))
		{
			log_message('error', 'An invalid name was submitted as the product name: '.$items['name'].' The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Prep the price.  Remove anything that isn't a number or decimal point.
		$items['price'] = trim(preg_replace('/([^0-9\.])/i', '', $items['price']));
		// Trim any leading zeros
		$items['price'] = trim(preg_replace('/(^[0]+)/i', '', $items['price']));

		// Is the price a valid number?
		if ( ! is_numeric($items['price']))
		{
			log_message('error', 'An invalid price was submitted for product ID: '.$items['id']);
			return FALSE;
		}

		// --------------------------------------------------------------------

		// We now need to create a unique identifier for the item being inserted into the cart.
		// Every time something is added to the cart it is stored in the master cart array.
		// Each row in the cart array, however, must have a unique index that identifies not only
		// a particular product, but makes it possible to store identical products with different options.
		// For example, what if someone buys two identical t-shirts (same product ID), but in
		// different sizes?  The product ID (and other attributes, like the name) will be identical for
		// both sizes because it's the same shirt. The only difference will be the size.
		// Internally, we need to treat identical submissions, but with different options, as a unique product.
		// Our solution is to convert the options array to a string and MD5 it along with the product ID.
		// This becomes the unique "row ID"
		if (isset($items['options']) AND count($items['options']) > 0)
		{
			$rowid = md5($items['id'].implode('', $items['options']));
		}
		else
		{
			// No options were submitted so we simply MD5 the product ID.
			// Technically, we don't need to MD5 the ID in this case, but it makes
			// sense to standardize the format of array indexes for both conditions
			$rowid = md5($items['id']);
		}

		// --------------------------------------------------------------------

		// Now that we have our unique "row ID", we'll add our cart items to the master array

		// let's unset this first, just to make sure our index contains only the data from this submission
		$old_data = FALSE;
		if(isset($this->_cart_contents[$rowid]))
		{
			$old_data = $this->_cart_contents[$rowid];
			unset($this->_cart_contents[$rowid]);
		}

		// Create a new index with our new row ID
		$this->_cart_contents[$rowid]['rowid'] = $rowid;

		// And add the new items to the cart array
		foreach ($items as $key => $val)
		{
			if($old_data && $key == 'qty')
			{
				$this->_cart_contents[$rowid][$key] = $val+$old_data[$key];
			}
			else
			{
				$this->_cart_contents[$rowid][$key] = $val;
			}
		}

		// Woot!
		return TRUE;
	}*/
}
?>