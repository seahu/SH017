<?php
// source: /var/www/seahu/app/presenters/templates/Modem/default.latte

class Template00f96ce9fb19a60e8b134c07ddb36d0f extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('7c3d64c6bb', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb01c929c6bb_content')) { function _lb01c929c6bb_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="modem">
	<h1> Modem </h1>
	<h2> Status: </h2>
	<table class="ser2net">
				<tr>
					<td>Satus modem:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($enable, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>Signal:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($signal, ENT_NOQUOTES) ?></td>
				</tr>
	</table>
	<h2> Setting: </h2>
	<table class="ser2net">
				<tr>
					<td>Device:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($device, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>Baud:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($baud, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>Dial:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($dial, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>APN:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($apn, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>IP:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($ip, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>Netmask:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($netmask, ENT_NOQUOTES) ?></td>
				</tr>
				<tr>
					<td>DNS:</td>
					<td><?php echo Latte\Runtime\Filters::escapeHtml($dns, ENT_NOQUOTES) ?></td>
				</tr>
	</table>
	<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Modem:configureModem"), ENT_COMPAT) ?>
"> Change Sttting</a><br><br>
	PS: actualy is supported USB modem: Huawei E3372 <br>with T-mobile operator in Czech Republic, other not be tested. <br>
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