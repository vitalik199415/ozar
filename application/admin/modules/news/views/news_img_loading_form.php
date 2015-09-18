<div class="files_upload" style="min-height:400px; text-align: center; color: #BBBBBB;">
<div class="fileupload-buttonbar img_upload_products">
	<div class="fileupload-buttons">
		<!-- The fileinput-button span is used to style the file input field as button -->
            <span class="fileinput-button fieldset flash">
                <span class="legend">Добавить файлы...</span>
                <input class="flash" type="file" name="Filedata" multiple>
            </span><BR><BR>
		<button type="submit" class="start">Загрузить все файлы</button>
		<button type="reset" class="cancel">Отменить загрузку</button>
		<button type="button" class="delete">Удалить выбранные</button>
<!--		<input type="checkbox" class="toggle">-->
		<!-- The global file processing state -->
		<span class="fileupload-process"></span>
	</div>
	<!-- The global progress state -->
	<div align="center">
	<div class="fileupload-progress fade" style="display:none">
		<!-- The global progress bar -->
		<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
		<!-- The extended global progress state -->
		<div class="progress-extended">&nbsp;</div>
	</div>
	</div>
</div>

<!-- The table listing the files available for upload/download -->
<div style="text-align: center; padding: 15px 0 0 0;" align="center">
	<table role="presentation" style="width:70%;" align="center">
		<tbody class="files">

		</tbody>
	</table>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress"></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="start" disabled>Загрузить...</button>
            {% } %}
            {% if (!i) { %}
                <button class="cancel">Отменить</button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="error">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <!--<td>
            <button class="delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Delete</button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>-->
    </tr>
{% } %}
</script>
    <div class="block" style="float:none; width:100%; margin:10px 0 0 0;">
        <div class="block_padding" id="photo_info">
            <?=$img_html?>
        </div>
    </div>
</div>