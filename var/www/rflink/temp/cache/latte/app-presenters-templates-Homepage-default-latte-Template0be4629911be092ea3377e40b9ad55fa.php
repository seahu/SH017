<?php
// source: /var/www/rflink/app/presenters/templates/Homepage/default.latte

class Template0be4629911be092ea3377e40b9ad55fa extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('8688a32943', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbd0896e47e5_content')) { function _lbd0896e47e5_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>	<div class="newblock">
		STATUS: <br>
		<div class="status">
<?php if ($status) { ?>
				RFlink service is running.
<?php } else { ?>
				RFlink service is topped.
<?php } ?>
		</div>
	</div>

	<br>
	<div>
		<div class="block">
			Log:<br>
			<div class="log">
<?php $iterations = 0; foreach ($log as $line) { ?>
					<?php echo Latte\Runtime\Filters::escapeHtml($line, ENT_NOQUOTES) ?> <br>
<?php $iterations++; } ?>
			</div>
			<br>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:"), ENT_COMPAT) ?>
">Refresh</a>
		</div>

		<div class="insert">
			<br>
		</div>

		<div class="block">
			View config file:<br>
				<div class="config">
<?php $iterations = 0; foreach ($config as $line) { ?>
					<?php echo Latte\Runtime\Filters::escapeHtml($line, ENT_NOQUOTES) ?> <br>
<?php $iterations++; } ?>
				</div>
		</div>
	</div>
<div class="newblock">
<br>
<b>RFlink</b><br>
is most flexible RF Gatway for control and collect data from wireless sensors on 433MHz. 
This solution discrem wide range of sesors, socket, remote controls, door bells, meteorological stations, etc. of many manufectures. Is suitable for create Home automation. RFLink have support for many home automation system etc.: Domoticz (tested), Jeedom, Pimatic, Domotiga, OpenHAB, HoMIDoM. <br>
Oreginal version of RFlink run on arduino and connect to PC via serial port emulated on USB. This is modification version run direct on raspberryPi mini PC, for comunication use TCP connection instead of serial connection. <br>
Becouse oreginal RFlink is not fully opensource, this version contain only public aviable modules, but so it is for many purpouses it is satisfactory.
<br>
PS: For scanning must be client connect to this service.
Only one connection to this service is allowed. Therefore connetc to this service may be your automation system, or for testing you can connect by telnet.
<br>
<br>
More on <a href="http://www.nemcon.nl/blog2/" class="help"> http://www.nemcon.nl/blog2/ </a> or <a href="http://www.seahu.cz"  class="help">http://www.seahu.cz</a> . <br>
If you can use this service in domoticz automation system please read this manual.
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
?>

<?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}