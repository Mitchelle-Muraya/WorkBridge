<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillRate extends Model
{
    protected $fillable = ['skill_name', 'min_rate', 'max_rate', 'average_rate'];
}

?>
