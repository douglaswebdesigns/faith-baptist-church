<div id="buddypress">
	<div class="activity" role="main">
		<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>

			<ul id="activity-stream" class="activity-list item-list">
			<?php while ( bp_activities() ) : bp_the_activity(); ?>

				<?php bp_get_template_part( 'activity/entry-single' ); ?>
				
			<?php endwhile; ?>
			</ul>

		<?php endif; ?>
	</div>
</div>