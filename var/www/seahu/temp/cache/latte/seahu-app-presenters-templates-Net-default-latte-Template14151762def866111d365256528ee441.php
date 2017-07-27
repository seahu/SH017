<?php
// source: /var/www/seahu/app/presenters/templates/Net/default.latte

class Template14151762def866111d365256528ee441 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('afd6ef0c03', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb1be59cc1f8_content')) { function _lb1be59cc1f8_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="net">
	<h1> Network Adapater Status </h1>
	<table>
		<tr>
			<td>Use DHCP client:</td>
			<td><?php echo Latte\Runtime\Filters::escapeHtml($template->replace($template->replace($dhcp, "1", "Yes"), "0", "No"), ENT_NOQUOTES) ?></td>
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
			<td>gateway</td>
			<td><?php echo Latte\Runtime\Filters::escapeHtml($gateway, ENT_NOQUOTES) ?></td>
		</tr>
		<tr>
			<td>Primary DNS server:</td>
			<td><?php echo Latte\Runtime\Filters::escapeHtml($dns, ENT_NOQUOTES) ?></td>
		</tr>
		<tr>
			<td>Mac address:</td>
			<td><?php echo Latte\Runtime\Filters::escapeHtml($mac, ENT_NOQUOTES) ?></td>
		</tr>
	</table>
<?php if ($user->loggedIn) { ?>
		<p><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Net:updateNetForm"), ENT_COMPAT) ?>
"> Edit</a></p>
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