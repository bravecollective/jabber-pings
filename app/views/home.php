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
						<th style="width: 60%;">Message</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($pings as $ping)
				{
					$user = ApiUser::find($ping->user_id);
					?>
					<tr>
						<td><?=$user->character_name?> (<?=$user->alliance_name?>)</td>
						<td><?=$ping->created_at?></td>
						<td><pre><?=$ping->message?></pre></td>
						<td>
							<a href="#" class="btn btn-info btn-xs viewPing">View Text</a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?=$pings->links()?>