<?php
// source: /var/www/rfx433MHz/app/components/scanRfxCode/scanRfxCode.latte

class Template11a8c7d662295884dd42fd766d03f2d7 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('b61f942215', 'html')
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
<div class="scanRfxCodeBox">
	<b>pokus s komponentou <?php echo Latte\Runtime\Filters::escapeHtml($name, ENT_NOQUOTES) ?></b>
<?php if ($config->scanning=="") { ?>
		<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("StartScanRFXCode!"), ENT_COMPAT) ?>
"> SStart scan</a><br>
		<?php echo Latte\Runtime\Filters::escapeHtml($config->code, ENT_NOQUOTES) ?>

<?php } else { ?>
		<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("StopScanRFXCode!"), ENT_COMPAT) ?>
"> Stop scan </a><br>
<?php } ?>
</div>
<?php
}}