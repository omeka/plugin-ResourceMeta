<?php
echo head(['title' => __('Resource Meta'), 'bodyclass'=>'show']);
echo flash();
?>
<h2><?php echo sprintf('%s: "%s"', __('Element Set'), __($element_set->name)); ?></h2>
<form method='post'>
    <section class="seven columns alpha">
        <span class="loading-span" style="color: #999;"><?php echo __('Loading table…'); ?></span>
        <div class="element-meta-names-table table-responsive" style="display: none;">
            <table>
                <thead>
                    <tr>
                        <th><?php echo __('Element'); ?></th>
                        <th><?php echo __('Meta Names'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($element_set->getElements() as $element): ?>
                    <?php
                    $value = null;
                    if (array_key_exists($element->id, $element_meta_names)) {
                        $value = $element_meta_names[$element->id];
                    }
                    ?>
                    <tr>
                        <td><?php echo __($element->name); ?></td>
                        <td>
                            <?php echo $this->formSelect(
                                sprintf('element_meta_names[%s][]', $element->id),
                                $value,
                                [
                                    'class' => 'element-meta-names-select',
                                    'data-placeholder' => __('Select meta names'),
                                    'style' => 'width: 100%;',
                                    'size' => '1',
                                ],
                                $meta_names
                            ); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), ['class' => 'big green button']); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
<script>
jQuery(function() {
    jQuery('.element-meta-names-select').chosen({
        width: '100%'
    });
    jQuery('.element-meta-names-table').show();
    jQuery('.loading-span').hide();
});
</script>
