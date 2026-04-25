<?php namespace NaEkb\Groups\Models;

/**
 * Model
 */
class GroupChange extends GroupMeeting
{
    /**
     * @var string table in the database used by the model.
     */
    public $table = 'naekb_group_changes';

    /**
     * @var array rules for validation.
     */
    public $rules = [
    ];

    public function filterFields($fields, $context = null)
    {

    }
}
