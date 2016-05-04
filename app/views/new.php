<div class="page-header" style="margin: 0 0 20px;">
	<h2>
		<a href="<?=URL::route('home')?>" class="pull-right btn btn-default">Back to Pings</a>
		New Ping
	</h2>
</div>

<?php
if (Session::has('flash_error'))
{
	?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div id="flash_error" class="alert alert-danger"><?=Session::get('flash_error')?></div>
		</div>
	</div>
	<?php
}
?>

<?=Form::open(array('route' => array('add_timer')))?>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="alert alert-danger">
				If you send <strong>shit pings</strong> you <strong>will be banned from sending pings.</strong>. 
				Use the template, it's there for a reason. <strong>Also, don't spam newlines in your pings or you'll have your ping rights revoked.</strong> 
				Make sure you send your pings to the correct group. <br /><br />
				<strong>This isn't hard.</strong> Don't be a dick.
			
			</div>

			<div class="alert alert-warning">
				If you are a FC: Please don't forget to <strong><a href="https://evetools.org/not_pap/" target="_blank">notpap</a></strong>!
			</div>

			<?php
			if(!empty($pingGroups))
			{
				?>
				<div class="form-group">
					<?=Form::label('pingGroup', 'Ping to this Group')?>
					<?=Form::select('pingGroup', $pingGroups, '', array('id' => 'pingGroup', 'class' => 'form-control'))?>
				</div>
				<?php
			}
			?>

			<div class="form-group">
				<?=Form::label('pingText', 'Ping Text')?>
				<?=Form::textarea('pingText', $defaultPingText, array('id' => 'pingText', 'class' => 'form-control'))?>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Send This Ping</button>
			</div>

		</div>
	</div>
</form>
