
<div class="action-holder action-holder--inactive">
	<div class="action-holder__inner">

		<div class="selected-tracks-info">
			<h5>Selected tracks:</h5>
			<div class="selected-tracks"></div>
			<div class="save-selected-tracks">
				<form>
					<input type="text" name="selected-tracks-playlist-name" value="Selected Tracks Playlist" />
					<input type="hidden" name="tracks-id" />
					<a href="requests/create-term-playlist.php?access-token=<?php echo $_GET['access-token']; ?>&playlisttitle=#PLAYLISTTITLE#&trackstoadd=" class="btn btn-primary"><strong>Save selected tracks as playlist</strong></a>
				</form>
			</div>
		</div>
	</div>
</div>

<footer>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<p>Power by <a href="https://www.spotify.com/" target="_blank">Spotify</a></p>
			</div>
		</div>
	</div>
</footer>

<script src="//code.jquery.com/jquery-3.1.0.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script>
	$( document ).ready(function() {
		$(function () {
			$('[data-toggle="popover"]').popover()
		});

		$('a[data-ajax-request]').click(function(e){
			var __clicked = $(this);
			e.preventDefault();
			var href = $(this).attr('href');
			$.ajax({
				url: href
			}).done(function(data) {
				__clicked.addClass('inactive');
				setTimeout(function(){
					messageHolder = __clicked.closest('.c2a-holder').find('.message-holder');
					__clicked.remove();
					messageHolder.append(data).addClass('message-holder--active');
				}, 200);
			});
		});

		$('#myTabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		var grid = $('.cards-section');
		grid.masonry({
			columnWidth: '.card-col-wrapper',
			itemSelector: '.card-col-wrapper'
		});

		$('.row--tracks-overview').click(function(){
			var trackIdsInput = $('.action-holder').find('[name=tracks-id]');
			var checkbox = $(this).find('[type=checkbox]');
			var checked = checkbox.prop('checked');
			var track = [];

			track.name = $(this).find('.track-name').text();
			track.id = $(this).find('.track-id').text();
			$(this).toggleClass('row--tracks-overview--active');

			if(checked === false){
				checkbox.prop('checked', 'checked');
				$('.selected-tracks').append('<span class="badge" data-track-id="'+ track.id +'" id="holder-'+ track.id +'">'+ track.name +'</span>');
				$('.action-holder.action-holder--inactive').removeClass('action-holder--inactive');
				trackIdsInput.val(trackIdsInput.val() + track.id + '|');

			} else {
				checkbox.prop('checked', false);
				$('.selected-tracks').find('#holder-'+track.id).fadeOut(100, function(){
					trackIdsInput.val(trackIdsInput.val().replace(track.id + '|', ''));
					$(this).remove();
					if($('.selected-tracks span').length == 0){
						$('.action-holder').addClass('action-holder--inactive');
					}
				});

			}
		});

		$('.selected-tracks').on('click', '.badge', function() {
			console.log('click');
			__this = $(this);
			$('tr#'+__this.data('track-id')).trigger('click');
		});

		$('.tracks-overview-filter table').DataTable({
			paging: false,
			searching: false,
			info: false
		});

		$('.save-selected-tracks').find('.btn').click(function(e){
			e.preventDefault();
			
			var __clicked = $(this);
			var href = __clicked.attr('href');
			href = href.replace('#PLAYLISTTITLE#', $('.action-holder').find('[name=selected-tracks-playlist-name]').val());
			href = href + $('.action-holder').find('[name=tracks-id]').val();

			$.ajax({
				url: href
			}).done(function(data) {
				messageHolder = $('.save-selected-tracks form');
				messageHolder.append(data);
			});
		});
	});
</script>

</body>
</html>