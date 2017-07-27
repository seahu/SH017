<?php
// source: /var/www/ser2net/app/presenters/templates/Homepage/default.latte

class Templatef3fbd8d6faf1723a7ffa41d75522f597 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('71a03c28b7', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb540fd33808_content')) { function _lb540fd33808_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="ser2net">
	<h1> Service bridge Serial port over network </h1>
	<p>
		Configure port:<?php if (isset($control_port)) { ?> <?php echo Latte\Runtime\Filters::escapeHtml($control_port, ENT_NOQUOTES) ?>
	<?php } else { ?> No defined	<?php } ?>

<?php if ($user->loggedIn) { ?>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:configurePortForm"), ENT_COMPAT) ?>
"> Change</a>
<?php } ?>
	</p>
	<p>
<?php if ($user->loggedIn) { ?>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:addSer2netForm"), ENT_COMPAT) ?>
"> Add new entry</a>
<?php } ?>
	</p>
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
<?php if ($user->loggedIn) { ?>
							<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:editSer2netForm", array($conf['id'])), ENT_COMPAT) ?>
"> Edit </a>
							<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:deleteQuery", array($conf['id'])), ENT_COMPAT) ?>
"> Del </a>
<?php } ?>
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
<?php if ($user->loggedIn) { ?>
			For apply change restart (stop and start) service from seahu menu
			<a href="/seahu/servicies" class="mybutton"> Service menu </a>.
<?php } ?>
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