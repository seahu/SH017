<?php
// source: /var/www/seahu/app/presenters/templates/Ser2net/default.latte

class Templatecef2feb1be324853a613591da01f0710 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('e32d0528d3', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbf8efa936e2_content')) { function _lbf8efa936e2_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="ser2net">
	<h1> Serial port over network </h1>
	<p>
		Configure port:<?php if (isset($control_port)) { ?> <?php echo Latte\Runtime\Filters::escapeHtml($control_port, ENT_NOQUOTES) ?>
	<?php } else { ?> No defined	<?php } ?>

		<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:configurePortForm"), ENT_COMPAT) ?>
"> Change</a>
	</p>
	<p><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:addSer2netForm"), ENT_COMPAT) ?>
"> Add new entry</a></p>
	<table class="ser2net">
				<tr>
					<th class="ser2net">Edit<br>Delete</th>
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
<?php if (count($ser2net_config) !== 0) { $iterations = 0; foreach ($ser2net_config as $conf) { ?>
				<tr>
					<td>
						<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:editSer2netForm", array($conf['id'])), ENT_COMPAT) ?>
"> Edit </a>
						<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Ser2net:deleteQuery", array($conf['id'])), ENT_COMPAT) ?>
"> Del </a>
					</td>
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
<?php $iterations++; } } else { ?>
			<tr>
				<td collspan='9'>No entry</td>
			</tr>
<?php } ?>
		</table>
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