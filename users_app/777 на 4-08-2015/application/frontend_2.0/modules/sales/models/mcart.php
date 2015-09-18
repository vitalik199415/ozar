<?php
class Mcart extends AG_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
	}

	public function add_item()
	{
		if(($id = intval($this->input->post('id'))) > 0)
		{
			$post = $this->input->post();
			if(!isset($post['price_id'])) return array('success' => FALSE, 'message' => $this->lang->line('cart_error_add_item'));
			$this->load->model('catalogue/mproducts');
			if($this->mproducts->check_isset_product($id, array('`status`' => 1, '`in_stock`' => 1)))
			{
				if($product_price = $this->mproducts->get_product_price($id, $post['price_id']))
				{

					$data['id'] = $post['id'];
					if(!isset($post['qty'])) $post['qty'] = $product_price['min_qty'];
					$data['qty'] = $post['qty'];
					if($data['qty'] < $product_price['min_qty'])
					{
						return array('success' => FALSE, 'message' => $this->lang->line('cart_error_min_qty_part1').$product_price['min_qty'].$this->lang->line('cart_error_min_qty_part2'));
					}

					$data['name'] = $post['id'];
					if($product_price['original_special_price']) $data['price'] = $product_price['original_special_price'];
					else $data['price'] = $product_price['original_price'];

					$data['options'] = array();
					$data['options']['price'] = $post['price_id'];
					if(isset($post['attributes']))
					{
						$data_attr = array();
						if($product_price['show_attributes'] == 1)
						{
							$attributes = $this->mproducts->get_product_attributes_and_options($data['id']);
							foreach($attributes as $key => $ms)
							{
								if(isset($post['attributes'][$key]))
								{
									foreach($ms as $keyop => $msop)
									{
										if($post['attributes'][$key] == $keyop)
										{
											$data_attr[$key] = $keyop;
										}
									}
								}
							}
						}
						else if($product_price['show_attributes'] == 2)
						{
							$attributes = $this->mproducts->get_product_attributes_and_options($data['id']);
							$id_attributes = explode(',', $product_price['id_attributes']);
							foreach($attributes as $key => $ms)
							{
								if(isset($post['attributes'][$key]) && in_array($key, $id_attributes))
								{
									foreach($ms as $keyop => $msop)
									{
										if($post['attributes'][$key] == $keyop)
										{
											$data_attr[$key] = $keyop;
										}
									}
								}
							}
						}
						$data['options'] += $data_attr;
					}

					$row_array = $this->cart->isset_cart_item($data);
					$this->load->model('warehouse/mwarehouses');
					if($wh_id = $this->mwarehouses->get_shop_wh_id())
					{
						$row_temp_array = $row_array;
						if(!$row_temp_array) $row_temp_array = $data + array('rowid' => FALSE);

						$pr_cart_qty = 0;
						$cart_temp = $this->cart->contents();
						foreach($cart_temp as $ms)
						{
							if($ms['id'] == $row_temp_array['id'] && $row_temp_array['rowid'] != $ms['rowid'])
							{
								if($product_temp_price = $this->mproducts->get_product_price($ms['id'], $ms['options']['price']))
								{
									$pr_cart_qty += $ms['qty']*$product_temp_price['real_qty'];
								}
							}
						}

						$query = $this->db->select("`qty`")
								->from("`".Mproducts::WH_PR."`")
								->where("`".Mproducts::ID_WH."`", $wh_id)->where("`".Mproducts::ID_PR."`", $id)->limit(1);
						if(count($wh_qty = $query->get()->row_array()) == 0) return array('success' => FALSE, 'message' => $this->lang->line('cart_error_in_wh'));
						$wh_qty = $wh_qty['qty'];
						if($wh_qty < $data['qty']*$product_price['real_qty'] + $pr_cart_qty && $pr_cart_qty == 0)
						{
							$available_qty = floor(($wh_qty - $pr_cart_qty)/$product_price['real_qty']);
							if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_error_qty_part1').$available_qty.'('.$available_qty*$product_price['real_qty'].$this->lang->line('cart_error_qty').')'.$this->lang->line('cart_error_qty_part2');
							else 								$message = $this->lang->line('cart_error_qty_part1').$available_qty.$this->lang->line('cart_error_qty_part2');
							return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
						}
						else if($wh_qty < $data['qty']*$product_price['real_qty'] + $pr_cart_qty && $pr_cart_qty > 0)
						{
							$available_qty = floor(($wh_qty - $pr_cart_qty)/$product_price['real_qty']);
							if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_error_qty_part1').$available_qty.'('.$available_qty*$product_price['real_qty'].$this->lang->line('cart_error_qty').')'.$this->lang->line('cart_error_qty_part2');
							else 								$message = $this->lang->line('cart_error_qty_part1').$available_qty.$this->lang->line('cart_error_qty_part2');
							return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
						}
					}

					if($row_array)
					{
						$this->cart->update(array('rowid' => $row_array['rowid'], 'qty' => $data['qty']));
						if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_success_add_item_item_already_exist_part1').$row_array['qty'].'('.$row_array['qty']*$product_price['real_qty'].$this->lang->line('cart_success_add_item_qty').') '.$this->lang->line('cart_success_add_item_item_already_exist_part2').$data['qty'].'('.$data['qty']*$product_price['real_qty'].$this->lang->line('cart_success_add_item_qty').').';
						else 								$message = $this->lang->line('cart_success_add_item_item_already_exist_part1').$row_array['qty'].' '.$this->lang->line('cart_success_add_item_item_already_exist_part2').$data['qty'].'.';
						return array('success' => TRUE, 'message' => $message);
					}
					else
					{
						$this->cart->insert($data);
						if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_success_add_item_part1').$data['qty'].'('.$data['qty']*$product_price['real_qty'].$this->lang->line('cart_success_add_item_qty').') '.$this->lang->line('cart_success_add_item_part2');
						else 								$message = $this->lang->line('cart_success_add_item_part1').$data['qty'].$this->lang->line('cart_success_add_item_qty').' '.$this->lang->line('cart_success_add_item_part2');
						return array('success' => TRUE, 'message' => $message);
					}
				}
			} else {
                return array('success' => FALSE, 'message' => $this->lang->line('cart_error_add_item_not_in_stock'));
            }
		}
		return array('success' => FALSE, 'message' => $this->lang->line('cart_error_add_item'));
	}

	public function edit_item()
	{
		$post = $this->input->post();
		$cart = $this->cart->contents();

		if(isset($post['rowid']) && isset($cart[$post['rowid']]) && isset($post['qty']) && ($post['qty'] = intval($post['qty'])) > 0)
		{
			$data = array(
				'rowid' => $post['rowid'],
				'qty'	=> $post['qty']
			);
			$this->load->model('catalogue/mproducts');
			$row_array = $cart[$post['rowid']];
			$product_price = $this->mproducts->get_product_price($row_array['id'], $row_array['options']['price']);

			$this->load->model('warehouse/mwarehouses');
			if($wh_id = $this->mwarehouses->get_shop_wh_id())
			{
				$pr_cart_qty = 0;
				$edit_item = $cart[$post['rowid']];
				$cart_temp = $cart;
				unset($cart_temp[$post['rowid']]);
				foreach($cart_temp as $ms)
				{
					if($ms['id'] == $edit_item['id'])
					{
						if($product_temp_price = $this->mproducts->get_product_price($ms['id'], $ms['options']['price']))
						{
							$pr_cart_qty += $ms['qty']*$product_temp_price['real_qty'];
						}
					}
				}

				$query = $this->db->select("`qty`")
						->from("`".Mproducts::WH_PR."`")
						->where("`".Mproducts::ID_WH."`", $wh_id)->where("`".Mproducts::ID_PR."`", $row_array['id'])->limit(1);
				if(count($wh_qty = $query->get()->row_array()) == 0) {$this->cart->update(array('rowid' => $data['rowid'], 'qty' => 0)); return array('success' => FALSE, 'message' => $this->lang->line('cart_error_eidt_in_wh'));}
				$wh_qty = $wh_qty['qty'];
				if($wh_qty < $data['qty']*$product_price['real_qty'] + $pr_cart_qty && $pr_cart_qty == 0)
				{
					$available_qty = floor(($wh_qty - $pr_cart_qty)/$product_price['real_qty']);
					if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_error_qty_part1').$available_qty.'('.$available_qty*$product_price['real_qty'].$this->lang->line('cart_error_qty').')'.$this->lang->line('cart_error_qty_part2');
					else 								$message = $this->lang->line('cart_error_qty_part1').$available_qty.$this->lang->line('cart_error_qty_part2');
					return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message, 'delay' => 7000);
				}
				else if($wh_qty < $data['qty']*$product_price['real_qty'] + $pr_cart_qty && $pr_cart_qty > 0)
				{
					$available_qty = floor(($wh_qty - $pr_cart_qty)/$product_price['real_qty']);
					if($product_price['real_qty'] > 1) 	$message = $this->lang->line('cart_error_qty_part1').$available_qty.'('.$available_qty*$product_price['real_qty'].$this->lang->line('cart_error_qty').')'.$this->lang->line('cart_error_qty_part2');
					else 								$message = $this->lang->line('cart_error_qty_part1').$available_qty.$this->lang->line('cart_error_qty_part2');
					return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message, 'delay' => 7000);
				}
			}
			$this->cart->update($data);
			if($product_price['real_qty'] > 1) $message = $this->lang->line('cart_success_edit_item_part1').$row_array['qty'].'('.$row_array['qty']*$product_price['real_qty'].$this->lang->line('cart_success_add_item_qty').') '.$this->lang->line('cart_success_edit_item_part2').$data['qty'].'('.$data['qty']*$product_price['real_qty'].$this->lang->line('cart_success_add_item_qty').').';
			else $message = $this->lang->line('cart_success_edit_item_part1').$row_array['qty'].' '.$this->lang->line('cart_success_edit_item_part2').$data['qty'].'.';
			return array('success' => TRUE, 'message' => $message);
		}
		else
		{
			return array('success' => FALSE, 'message' => $this->lang->line('cart_error_edit_item'), 'delay' => 5000);
		}
	}

	public function delete_item()
	{
		$post = $this->input->post();
		if(isset($post['rowid']))
		{
			$cart = $this->cart->contents();
			if($cart[$post['rowid']])
			{
				$data = array(
					'rowid' => $post['rowid'],
					'qty'	=> 0
				);
				$this->cart->update($data);
				return array('success' => TRUE, 'message' => $this->lang->line('cart_success_delete_item'));
			}
			return array('success' => FALSE, 'message' => $this->lang->line('cart_error_delete_item_no_item'));
		}
		return array('success' => FALSE, 'message' => $this->lang->line('cart_error_delete_item'));
	}

	public function get_cart_short()
	{
		$this->load->model('catalogue/mcurrency');
		$currency = $this->mcurrency->get_current_currency();
		$data['currency_name'] = $currency['name'];
		$data['total_price'] = number_format($this->cart->total() * $currency['rate'], 2, ',', ' ').' '.$currency['name'];
		$data['total_items'] = $this->cart->total_items();
		return $data;
	}

	public function get_cart_products()
	{
		$data = array();
		$cart = $this->cart->contents();
		if(count($cart)>0)
		{
			$this->load->model('catalogue/mproducts');
			$data = $this->mproducts->get_cart_products($cart);
			$currency = $this->mcurrency->get_current_currency();
			return array('cart_products' => $data, 'cart_total_price' => number_format($this->cart->total() * $currency['rate'], 2, ',', ' ').' '.$currency['name']);
		}
		return FALSE;
	}
}