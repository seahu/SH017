<?php
// source: /var/www/rfx433MHz/app/components/evolveoOpeningDetector/evolveoOpeningDetector.latte

class Templatea82b4d6745355a81ed85c2b6e1689f49 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('4a04dba201', 'html')
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
							<a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->alarm->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("AlarmButton!"), ENT_COMPAT) ?>
"> ON </a>
						</td>
						<td>
							<div class="last_date">
								last:
								<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->alarm->lastContact, 'Y-m-d'), ENT_NOQUOTES) ?>

								<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->alarm->lastContact, 'H:i:s'), ENT_NOQUOTES) ?>

							</div>
						</td>
						<td></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->alarm->name, ENT_NOQUOTES) ?></td><br>
					</tr>
					<tr>
						<td>
							<a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->penetrate->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("PenetrateButton!"), ENT_COMPAT) ?>
"> ON </a>
						</td>
						<td>
							<div class="last_date">
								last:
								<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->penetrate->lastContact, 'Y-m-d'), ENT_NOQUOTES) ?>

								<?php echo Latte\Runtime\Filters::escapeHtml($template->date($config->penetrate->lastContact, 'H:i:s'), ENT_NOQUOTES) ?>

							</div>
						</td>
						<td></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->penetrate->name, ENT_NOQUOTES) ?></td><br>
					</tr>					
					<tr>
						<td>
							<a  class="mybutton_OFF" title="Reset status alarm, status penetrate and alarm battery level." href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("ResetButton!"), ENT_COMPAT) ?>
"> RESET </a><br>
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