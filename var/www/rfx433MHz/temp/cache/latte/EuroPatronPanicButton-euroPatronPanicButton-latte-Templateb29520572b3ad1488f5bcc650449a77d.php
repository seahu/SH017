<?php
// source: /var/www/rfx433MHz/app/components/EuroPatronPanicButton/euroPatronPanicButton.latte

class Templateb29520572b3ad1488f5bcc650449a77d extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('cdeadd8790', 'html')
;
// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
?>
<div class="box">
	<table>
		<tr>
			<td>
				<table class="small">
					<tr>
						<td>
							<a  class="mybutton_ON" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("PushButton!"), ENT_COMPAT) ?>
"> ON </a>
						</td>
						<td></td>
						<td></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td>
							<div class="last_date">
							last: 
							<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->lastPush, 'Y-m-d'), ENT_NOQUOTES) ?>

							<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->lastPush, 'H:i:s'), ENT_NOQUOTES) ?>

							</div>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("ScanRFXCode!"), ENT_COMPAT) ?>
"> Scan code </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Rename!"), ENT_COMPAT) ?>
"> Set Name </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("AddDomoticz!"), ENT_COMPAT) ?>
"> Add Domoticz </a><br>
			</td>
		</tr>
	</table>
</div>
<?php
}}