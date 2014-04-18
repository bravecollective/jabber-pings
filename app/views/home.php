<?php
use Carbon\Carbon;

if (Session::has('flash_error'))
{
	?>
	<div id="flash_error" class="alert alert-danger"><?=Session::get('flash_error')?></div>
	<?php
}

if (Session::has('flash_msg'))
{
	?>
	<div id="flash_error" class="alert alert-info"><?=Session::get('flash_msg')?></div>
	<?php
}
?>

<div class="row">
	<div class="col-lg-12">
		<h3>
			<?php
			if(Auth::user()->permission === 1)
			{
				?>
				<a href="<?=URL::to('new')?>" class="btn btn-success pull-right btn-sm">New</a>
				<?php
			}
			?>
			Recent Pings
		</h3>
		<div>
			<table class="table table-bordered table-striped table-hover table-condensed ">
				<thead>
					<tr>
						<th>Name</th>
						<th>Date</th>
						<th>Message</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($pings as $ping)
				{
					?>
					<tr>
						<td><?=$ping?></td>
						<td><?=$ping?></td>
						<td><pre><?=$ping?></pre></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>