<?php
// source: /var/www/seahu/app/presenters/templates/Openvpn/default.latte

class Templateec2fb984fff4f7afeba89dd2a19011bf extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('788aef0934', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb1f25cee16b_content')) { function _lb1f25cee16b_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="net">
	<h1> OpenVPN Status </h1>
<?php if ($enable) { ?>
		<table>
			<tr>
				<td>Status:</td>
				<td><b>Enabled</b></td>
			</tr>
			<tr>
				<td>IP local:</td>
				<td><?php echo Latte\Runtime\Filters::escapeHtml($ip1, ENT_NOQUOTES) ?></td>
			</tr>
			<tr>
				<td>IP remote:</td>
				<td><?php echo Latte\Runtime\Filters::escapeHtml($ip2, ENT_NOQUOTES) ?></td>
			</tr>
			<tr>
				<td>netmask:</td>
				<td>
					<?php echo Latte\Runtime\Filters::escapeHtml($netmask, ENT_NOQUOTES) ?>

				</td>
			</tr>
		</table>
<?php } else { ?>
		OpenVPN is disable
<?php } if ($user->loggedIn) { ?>
		<p><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Openvpn:updateOpenvpnForm"), ENT_COMPAT) ?>
"> Edit openVPN setting</a></p>
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