<?php
/**
 * Fight page
 */

$id = Yii::$app->user->getIdentity()->cid;
$params = [
	'owner' => $owner,
	'id' => $fight->id,
	'userId' => $game->getPlayer1()->getUser()->id
];
if(!$owner){
	$params['opponentId'] = $game->getPlayer2()->getUser()->id;
}
$data = \yii\helpers\Json::encode($params);
$this->registerJs("
	$.extend(fight, {$data});
	fight.init();
");
?>
<div class="row">
	<div class="col-md-9">
		<div id="game-holder">
			<div id="player-holder-1" class="player-holder">
				<?php
				echo $this->render('player', ['game' => $game, 'player' => $game->getPlayer1()]);
				?>
			</div>

			<div class="game-field empty" id="game-field">
				Ожидание противника
				<span class="point-blk"></span>
			</div>

			<div id="player-holder-2" class="player-holder">
				<?php if (!$owner) {
					echo $this->render('player', ['game' => $game, 'player' => $game->getPlayer2()]);
				} ?>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="chat" id="chat">
			<div class="holder"></div>
			<div class="input-group c-row">
				<input type="text" class="form-control" aria-label="">
				<span class="input-group-addon">
					<a class="btn btn-success">
						<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
					</a>
				</span>
			</div>
		</div>
	</div>
</div>