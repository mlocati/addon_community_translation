<?php
use Concrete\Package\CommunityTranslation\Src\Service\Access;

/* @var Concrete\Core\Page\View\PageView $this */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Localization\Service\Date $dh */

defined('C5_EXECUTE') or die('Access Denied.');

id(new Area('Opening'))->display($c);

?>
<div class="panel panel-default">
    <div class="panel-heading"><h3><?php echo t('Translators Teams'); ?></h3></div>
    <div class="panel-body">
        <table class="table table-hover">
            <tbody><?php
               foreach ($approved as $l) {
                    ?><tr>
                        <td><a href="<?php echo URL::to('/teams/details', $l['id']); ?>"><?php echo h($l['name']); ?></a></td>
                        <td><?php
                           if (!isset($me)) {
                               ?><a class="btn btn-sm btn-default pull-right" href="#" onclick="<?php echo h('alert('.json_encode(t('You must sign-in in order to join this translation group.')).'); return false'); ?>"><?php echo t('Join'); ?></a><?php
                           } else {
                               switch ($l['access']) {
                                   case Access::GLOBAL_ADMIN:
                                       break;
                                   case Access::ADMIN:
                                   case Access::TRANSLATE:
                                       ?><form method="POST" action="<?php echo $this->action('leave', $l['id']); ?>" onsubmit="<?php echo h('return confirm('.json_encode('Are you sure?').')'); ?>">
                                           <?php $token->output('comtra_leave'.$l['id']); ?>
                                           <input type="submit" class="btn btn-sm btn-danger pull-right" value="<?php echo h(t('Leave')); ?>" /> 
                                       </form><?php
                                       break;
                                   case Access::ASPRIRING:
                                       ?><form method="POST" action="<?php echo $this->action('cancel_request', $l['id']); ?>" onsubmit="<?php echo h('return confirm('.json_encode('Are you sure?').')'); ?>">
                                           <?php $token->output('comtra_cancel_request'.$l['id']); ?>
                                           <input type="submit" class="btn btn-sm btn-warning pull-right" value="<?php echo h(t('Cancel request')); ?>" /> 
                                       </form><?php
                                       break;
                                   case Access::NONE:
                                       ?><form method="POST" action="<?php echo $this->action('join', $l['id']); ?>" onsubmit="<?php echo h('return confirm('.json_encode('Are you sure?').')'); ?>">
                                           <?php $token->output('comtra_join'.$l['id']); ?>
                                           <input type="submit" class="btn btn-sm btn-success pull-right" value="<?php echo h(t('Join')); ?>" /> 
                                       </form><?php
                                       break;
                               }
                           }
                        ?></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div>
</div>

<?php
if (!empty($requested)) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading"><h3><?php echo t('Requested Teams'); ?></h3></div>
        <div class="panel-body">
            <table class="table table-hover">
                <tbody><?php
                   foreach ($requested as $l) {
                        ?><tr>
                            <td>
                                <b><?php echo h($l['name']); ?></b><br />
                                <?php echo tc('Language', 'Requested by: %s', $l['requestedBy'] ? h($l['requestedBy']->getUserName()) : '?'); ?><br />
                                <?php echo tc('Language', 'Requested on: %s', $dh->formatPrettyDateTime($l['requestedOn'], true, true)); ?>
                            </td>
                            <td><?php
                                if ($l['canApprove']) {
                                   ?><form method="POST" action="<?php echo $this->action('approve_locale', $l['id']); ?>" onsubmit="<?php echo h('return confirm('.json_encode('Are you sure?').')'); ?>">
                                       <?php $token->output('comtra_approve_locale'.$l['id']); ?>
                                       <input type="submit" class="btn btn-sm btn-success pull-right" value="<?php echo h(t('Approve')); ?>" /> 
                                   </form><?php
                                }
                                if ($l['canCancel']) {
                                   ?><form method="POST" action="<?php echo $this->action('cancel_locale', $l['id']); ?>" onsubmit="<?php echo h('return confirm('.json_encode('Are you sure?').')'); ?>">
                                       <?php $token->output('comtra_cancel_locale'.$l['id']); ?>
                                       <input type="submit" class="btn btn-sm btn-danger pull-right" value="<?php echo h(t('Cancel')); ?>" /> 
                                   </form><?php
                               }
                            ?></td>
                        </tr><?php
                    }
                ?></tbody>
            </table>
        </div>
    </div>
    <?php
}

id(new Area('Closing'))->display($c);