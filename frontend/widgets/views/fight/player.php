<?php
/**
 * Player block
 */
use yii\helpers\Html;

?>

<div class="player-block">
	<div class="row">
		<div class="col-md-7">
			<ul class="card-list">
			<?php for ($i = 1; $i <= $game->getSettings()->maxCards; $i++) { ?>
				<li class="empty">
					<span>
						<?php echo $i; ?>
					</span>
				</li>
			<?php } ?>
			</ul>
		</div>
		<div class="col-md-5">
			<div class="c-holder">
				<div class="m-point-info">
					<span>
						<?php echo $player->data->getPoints(); ?>
					</span> /
					<?php echo $player->data->getMaxPoint(); ?>
				</div>
				<span class="m-point progress" data-max="10">
					<span style="height: 100%;" data-value="10"></span>
				</span>
				<span class="img">
					<?php echo Html::img($player->user->img->getThumb('80x80'), [
						'alt' => $player->user->username,
						'title' => $player->user->username
					]); ?>
				</span>
				<span class="health">
					<span>
						<?php echo $player->data->getHealth(); ?>
					</span>
					<div class="message"></div>
				</span>
			</div>
		</div>
	</div>
</div>