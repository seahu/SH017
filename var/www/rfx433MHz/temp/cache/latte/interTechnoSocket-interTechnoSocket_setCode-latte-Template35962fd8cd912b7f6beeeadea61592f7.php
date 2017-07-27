<?php
// source: /var/www/rfx433MHz/app/components/interTechnoSocket/interTechnoSocket_setCode.latte

class Template35962fd8cd912b7f6beeeadea61592f7 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('ee1e9a7b1a', 'html')
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
				<table bgcolor="#AD0000">
					<tr>
						<td colspan="2"><font color="white">ON </font></td>
						<td colspan="<?php echo Latte\Runtime\Filters::escapeHtml($codeLen-2, ENT_COMPAT) ?>" align="right"><font color="white">DIP </font></td>
					</tr>
					
					<tr>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Latte\Runtime\CachingIterator($code) as $item) { if ($item) { ?>
								<td><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetRFXpartCode!", array($iterator->counter-1, 'off')), ENT_COMPAT) ?>
"> <image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/switch_on.svg"> </a></td>
<?php } else { ?>
								<td><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetRFXpartCode!", array($iterator->counter-1, 'on')), ENT_COMPAT) ?>
"> <image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/switch_off.svg"> </a></td>
<?php } $iterations++; } array_pop($_l->its); $iterator = end($_l->its) ?>
					</tr>
					<tr>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Latte\Runtime\CachingIterator($code) as $item) { ?>
							<td align="center"><font color="white"><?php echo Latte\Runtime\Filters::escapeHtml($iterator->counter, ENT_NOQUOTES) ?></font></td>
<?php $iterations++; } array_pop($_l->its); $iterator = end($_l->its) ?>
					</tr>
				</table>
				<br>			
				switch no. 1-26  - family socket code (67 mil. combination)<br>
				switch no. 27-30 - button code (16 combinations)
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Control!"), ENT_COMPAT) ?>
"> Control </a><br>
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