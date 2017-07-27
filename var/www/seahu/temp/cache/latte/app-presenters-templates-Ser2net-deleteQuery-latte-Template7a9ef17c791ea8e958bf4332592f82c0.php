<?php
// source: /var/www/seahu/app/presenters/templates/Ser2net/deleteQuery.latte

class Template7a9ef17c791ea8e958bf4332592f82c0 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('19e03c9191', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb426f6f1f3f_content')) { function _lb426f6f1f3f_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="ser2net">
	<h1> Serial port over network </h1>
	<h2> You sure to delete next item: </h2>
	<table class="ser2net">
				<tr>
					<th class="ser2net">Device<br>path</th>
					<th>Port<br>number</th>
					<th>Baud<br>rate</th>
					<th>Time<br> out</th>
					<th>Parity</th>
					<th>Stop<br>bits</th>
					<th>Data<br>bit</th>
					<th>Flow<br>control</th>
					<th>Remote controls<br>(by RFC 2217)</th>
				</tr>
				<tr>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['device'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['port'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['rate'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['timeout'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['parity'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['stopBit'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['dataBit'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($conf['flow'], ENT_NOQUOTES) ?></td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($template->replace($conf['remoteContol']?:"No", "remctl", "Yes"), ENT_NOQUOTES) ?></td>
				</tr>
		</table>
		<p>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:"), ENT_COMPAT) ?>
"> No</a>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:deleteItem", array($noID)), ENT_COMPAT) ?>
"> Yes</a>
		</p>
</div>
<?php
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start(function () {});}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}