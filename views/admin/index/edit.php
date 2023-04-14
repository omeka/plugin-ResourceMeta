<?php
echo head(array('title' => __('Resource Meta'), 'bodyclass'=>'show'));
echo flash();
?>
<h2><?php echo sprintf('%s: "%s"', __('Element Set'), __($element_set->name)); ?></h2>
<form method='post'>
    <section class="seven columns alpha">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th><?php echo __('Element'); ?></th>
                        <th><?php echo __('Meta Names'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php // @todo: Add rows of element names / meta name multi-selects ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), array('class' => 'big green button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
