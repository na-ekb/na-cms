<?php

use October\Rain\Database\Updates\Migration;

/**
 * Убираем неразрывные пробелы (U+00A0, UTF-8 0xC2A0) из дополнительной информации
 * к адресу групп (address_desc). Теперь в доп. адресе используются обычные пробелы;
 * неразрывные остаются только для основного адреса и добавляются на этапе вывода.
 *
 * REPLACE по байтам 0xC2A0 не зависит от коллации и не трогает строки без nbsp.
 */
return new class extends Migration
{
    public function up()
    {
        Db::table('naekb_groups')->update([
            'address_desc' => Db::raw("REPLACE(address_desc, 0xC2A0, ' ')")
        ]);
    }

    public function down()
    {
        // Необратимо: исходные неразрывные пробелы не восстанавливаем.
    }
};
