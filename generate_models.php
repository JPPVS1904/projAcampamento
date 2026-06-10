<?php

$tables = [
    'marital_statuses' => ['model' => 'MaritalStatus', 'fields' => ['title']],
    'sectors' => ['model' => 'Sector', 'fields' => ['name', 'place']],
    'selection_methods' => ['model' => 'SelectionMethod', 'fields' => ['method', 'description', 'type']],
    'sections' => ['model' => 'Section', 'fields' => ['title', 'order']],
    'categories' => ['model' => 'Category', 'fields' => ['name', 'type']],
    'campings' => ['model' => 'Camping', 'fields' => ['notice', 'term', 'image', 'minimal_age', 'maximal_age', 'camper_fee', 'servant_fee', 'planned_man_vacancies', 'planned_woman_vacancies', 'planned_couple_vacancies', 'raffle_man_vacancies', 'raffle_woman_vacancies', 'raffle_couple_vacancies', 'raffle_total_vacancies', 'raffle_camper_subscription_start_date', 'raffle_camper_subscription_end_date', 'raffle_camper_date', 'raffle_servant_subscription_start_date', 'raffle_servant_subscription_end_date', 'raffle_servant_date', 'camper_registration_start_date', 'camper_registration_end_date', 'camper_payment_link', 'camper_payment_date', 'servant_registration_start_date', 'servant_registration_end_date', 'servant_payment_link', 'servant_payment_date']],
    'events' => ['model' => 'Event', 'fields' => ['minimal_age', 'is_paid_festival', 'ticket_price', 'sale_start_date', 'payment_link']],
    'users' => ['model' => 'User', 'fields' => ['cpf', 'name', 'birthday', 'sex', 'phone', 'email', 'photo', 'is_counselor', 'is_admin', 'password', 'access_token', 'refresh_token', 'marital_status_id']],
    'questions' => ['model' => 'Question', 'fields' => ['text', 'order', 'type', 'accept_generic_answer', 'section_id', 'depends_on_option_id']],
    'options' => ['model' => 'Option', 'fields' => ['text', 'question_id']],
    'addresses' => ['model' => 'Address', 'fields' => ['street', 'number', 'neighborhood', 'city', 'cep', 'user_id']],
    'activities' => ['model' => 'Activity', 'fields' => ['name', 'image', 'place', 'year', 'start_date', 'duration_days', 'total_vacancies', 'category_id', 'activitable_type', 'activitable_id']],
    'camping_pre_registration' => ['model' => 'CampingPreRegistration', 'fields' => ['substitute_position', 'is_quitter', 'selection_method_id', 'spouse_id', 'sector_id', 'sector2_id'], 'table' => 'camping_pre_registration'],
    'pre_registrations' => ['model' => 'PreRegistration', 'fields' => ['subscription_type', 'is_fee_paid', 'payment_code', 'qrcode_data', 'is_qrcode_used', 'user_id', 'activity_id', 'camping_pre_registration_id']],
    'answers' => ['model' => 'Answer', 'fields' => ['pre_registration_id', 'question_id', 'answer']],
];

foreach ($tables as $table => $data) {
    $modelName = $data['model'];
    $fields = $data['fields'];
    $tableName = $data['table'] ?? $table;
    
    $fillableArray = implode(",\n    ", array_map(function($f) { return "'$f'"; }, $fields));
    
    $content = "<?php\n\nnamespace App\Models;\n\n";
    $content .= "use Illuminate\Database\Eloquent\Factories\HasFactory;\n";
    $content .= "use Illuminate\Database\Eloquent\Model;\n";
    $content .= "use Illuminate\Database\Eloquent\SoftDeletes;\n\n";
    
    $content .= "class $modelName extends Model\n{\n";
    $content .= "    use HasFactory, SoftDeletes;\n\n";
    if (isset($data['table'])) {
        $content .= "    protected \$table = '$tableName';\n\n";
    }
    if ($modelName === 'CampingPreRegistration') {
        $content .= "    public \$timestamps = false;\n\n";
    }
    $content .= "    protected \$fillable = [\n        $fillableArray\n    ];\n";
    
    if ($modelName === 'User') {
        $content .= "\n    protected \$hidden = [\n        'password',\n        'remember_token',\n    ];\n";
        $content .= "\n    protected function casts(): array\n    {\n        return [\n            'birthday' => 'date',\n            'password' => 'hashed',\n            'is_counselor' => 'boolean',\n            'is_admin' => 'boolean',\n        ];\n    }\n";
    }
    
    $content .= "}\n";
    
    file_put_contents(__DIR__ . "/app/Models/$modelName.php", $content);
}

echo "Models generated.\n";
