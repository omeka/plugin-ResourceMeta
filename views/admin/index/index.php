<?php
echo head(['title' => __('Resource Meta'), 'bodyclass'=>'show']);
echo flash();
?>
<h2><?php echo __('Element Sets'); ?></h2>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
                <th><?php echo __('Meta names count'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (loop('element_sets') as $elementSet): ?>
            <tr>
                <td class="element-set-name">
                    <?php echo html_escape(__($elementSet->name)); ?>
                    <ul class="action-links">
                        <li><a href="<?php echo html_escape(url(['controller' => 'resource-meta', 'action' => 'edit', 'id' => $elementSet->id], 'resource-meta/id')); ?>"><?php echo __('Edit'); ?></a></li>
                    </ul>
                </td>
                <td>
                    <?php echo html_escape(__($elementSet->description)); ?>
                </td>
                <td>
                    <?php echo isset($element_meta_names[$elementSet->id]) ? array_sum(array_map('count', $element_meta_names[$elementSet->id])) : '0'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo foot(); ?>
