<div id="cd-vehicle-compare-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-full" role="document">
		<div class="modal-content cd-vehicle-compare-modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php cdhl_compare_popup_title(); ?></h4>
			</div>
			<div class="modal-body cd-vehicle-compare-modal-body">
				<div class="cd-vehicle-compare-loader"><?php echo esc_html__( 'Loading...', 'cardealer' ); ?></div>
				<div class="cd-vehicle-compare-content"></div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
