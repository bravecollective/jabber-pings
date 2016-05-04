<div id="footer">
	<div class="container">
		<p class="text-muted credit">
			<!--<span class="pull-right">Help</span>-->
			© HERO - 2013
		</p>
	</div>
</div>

<div class="modalBuilder">
	<!-- Modal -->
	<div class="modal fade" id="pingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Ping Reciept</h4>
				</div>
				<div class="modal-body">
					<h4>Ping Headers:</h4>
					<div class="alert alert-warning">
						<strong>From:</strong> <span class="pingUser"></span> <br />
						<strong>Date:</strong> <span class="pingTime"></span> <br />
						<strong>To:</strong> <span class="pingGroup"></span>
					</div>
					<h4>Ping Contents:</h4>
					<div class="pingBody"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function()
	{

		$('.viewPing').on('click', function()
		{
			var tr = $(this).parents('tr');

			var user = tr.children('td:eq(0)').html();
			var time = tr.children('td:eq(1)').html();
			var group  = tr.children('td:eq(2)').html();
			var msg = tr.children('td:eq(3)').html();

			$('.pingUser', '.modalBuilder .modal').html(user);
			$('.pingTime', '.modalBuilder .modal').html(time);
			$('.pingGroup', '.modalBuilder .modal').html(group);
			$('.pingBody', '.modalBuilder .modal').html(msg);

			$('#pingModal').modal({
				keyboard: true
			});
		});


		$('[data-toggle=tooltip]').tooltip();
	});
</script>