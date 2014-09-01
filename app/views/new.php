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

			<div class="checkbox">
				<?=Form::hidden('legacyPing', '0')?>
				<label>
					<?=Form::checkbox('legacyPing', 1, true, array('id' => 'legacyPing'))?>
					Ping To Old Jabber Server?
				</label>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Send Ping</button>
			</div>

		</div>
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function()
	{
		//$('#timeExiting').datetimepicker();
	});
</script>