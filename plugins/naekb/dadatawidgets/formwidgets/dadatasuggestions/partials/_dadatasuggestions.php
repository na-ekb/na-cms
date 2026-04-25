<?php if ($this->previewMode) { ?>
    <div class="form-control">
        <?= $value ?>
    </div>
<?php } else { ?>
    <select id="dadata-widget-<?= $this->getId('input') ?>" name="selectionMode" class="form-control dadata-select w-150" data-control="selection-mode" data-token="<?=$dadataToken?>" data-url="<?=$dadataUrl?>"></select>
    <script>
        <?php if(!$relation) { ?>
        document.addEventListener('DOMContentLoaded', function () {
        <?php } ?>
            $('#dadata-widget-<?= $this->getId('input') ?>').on('select2:select', function(e) {
                let item = e.params.data.data;
                console.log(item);
                <?php if(isset($map)):?>
                <?php foreach ($map as $field => $dadataValue):?>
                if (document.querySelector('[name="<?=$field?>"]') !== undefined) {
                    if (item.data.<?=$dadataValue?> !== undefined) {
                        document.querySelector('[name="<?=$field?>"]').value = item.data.<?=$dadataValue?>;
                    } else if ('<?=$dadataValue?>' === 'address') {
                        let data = item.value.split(item.data.street_with_type);
                        document.querySelector('[name="<?=$field?>"]').value = item.data.street_with_type + data.pop();
                    }
                }
                <?php endforeach;?>
                <?php endif?>
            });
        <?php if(!$relation) { ?>
        });
        <?php } ?>
    </script>
<?php } ?>
