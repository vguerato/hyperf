<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'O campo :attribute deve ser aceito.',
    'accepted_if' => 'O campo :attribute deve ser aceito quando :other for :value.',
    'active_url' => 'O campo :attribute não é uma URL válida.',
    'after' => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash' => 'O campo :attribute deve conter apenas letras, números e traços.',
    'alpha_num' => 'O campo :attribute deve conter apenas letras e números.',
    'array' => 'O campo :attribute deve ser um array.',
    'ascii' => 'O campo :attribute deve conter apenas caracteres alfanuméricos e símbolos de byte único.',
    'before' => 'O campo :attribute deve ser uma data anterior a :date.',
    'before_or_equal' => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'file' => 'O campo :attribute deve ter entre :min e :max kilobytes.',
        'string' => 'O campo :attribute deve ter entre :min e :max caracteres.',
        'array' => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação de :attribute não corresponde.',
    'contains' => 'O campo :attribute está faltando um valor obrigatório.',
    'date' => 'O campo :attribute não é uma data válida.',
    'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format' => 'O campo :attribute não corresponde ao formato :format.',
    'decimal' => 'O campo :attribute deve ter :decimal casas decimais.',
    'declined' => 'O campo :attribute deve ser recusado.',
    'declined_if' => 'O campo :attribute deve ser recusado quando :other for :value.',
    'different' => 'Os campos :attribute e :other devem ser diferentes.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute possui dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute possui um valor duplicado.',
    'doesnt_end_with' => 'O campo :attribute não deve terminar com um dos seguintes: :values.',
    'doesnt_start_with' => 'O campo :attribute não deve começar com um dos seguintes: :values.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes: :values.',
    'enum' => 'O :attribute selecionado é inválido.',
    'exclude' => 'O campo :attribute está excluído.',
    'exclude_if' => 'O campo :attribute está excluído quando :other for :value.',
    'exclude_unless' => 'O campo :attribute está excluído a menos que :other esteja em :values.',
    'exclude_with' => 'O campo :attribute está excluído quando :values está presente.',
    'exclude_without' => 'O campo :attribute está excluído quando :values não está presente.',
    'exists' => 'O :attribute selecionado é inválido.',
    'extensions' => 'O campo :attribute deve ter uma das seguintes extensões: :values.',
    'file' => 'O campo :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute é obrigatório.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value',
        'file' => 'O campo :attribute deve ser maior que :value kb',
        'string' => 'O campo :attribute deve ser maior que :value caracteres',
        'array' => 'O campo :attribute deve ter mais de :value itens',
    ],
    'gte' => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value',
        'file' => 'O campo :attribute deve ser maior ou igual a :value kb',
        'string' => 'O campo :attribute deve ser maior ou igual a :value caracteres',
        'array' => 'O campo :attribute deve ter :value itens ou mais',
    ],
    'hex_color' => 'O campo :attribute deve ser uma cor hexadecimal válida.',
    'image' => 'O campo :attribute deve ser uma imagem.',
    'in' => 'O :attribute selecionado é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'ip' => 'O campo :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'list' => 'O campo :attribute deve ser uma lista.',
    'lowercase' => 'O campo :attribute deve estar em minúsculas.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value',
        'file' => 'O campo :attribute deve ser menor que :value kb',
        'string' => 'O campo :attribute deve ser menor que :value caracteres',
        'array' => 'O campo :attribute deve ter menos de :value itens',
    ],
    'lte' => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value',
        'file' => 'O campo :attribute deve ser menor ou igual a :value kb',
        'string' => 'O campo :attribute deve ser menor ou igual a :value caracteres',
        'array' => 'O campo :attribute deve ter :value itens ou menos',
    ],
    'mac_address' => 'O campo :attribute deve ser um endereço MAC válido.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file' => 'O campo :attribute não pode ter mais de :max kilobytes.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
        'array' => 'O campo :attribute não pode ter mais de :max itens.',
    ],
    'max_digits' => 'O campo :attribute não deve ter mais de :max dígitos.',
    'mimes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'file' => 'O campo :attribute deve ter no mínimo :min kilobytes.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        'array' => 'O campo :attribute deve ter no mínimo :min itens.',
    ],
    'min_digits' => 'O campo :attribute deve ter pelo menos :min dígitos.',
    'missing' => 'O campo :attribute deve estar ausente.',
    'missing_if' => 'O campo :attribute deve estar ausente quando :other for :value.',
    'missing_unless' => 'O campo :attribute deve estar ausente a menos que :other seja :value.',
    'missing_with' => 'O campo :attribute deve estar ausente quando :values estiver presente.',
    'missing_with_all' => 'O campo :attribute deve estar ausente quando :values estiverem presentes.',
    'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    'not_in' => 'O :attribute selecionado é inválido.',
    'not_regex' => 'O campo :attribute não pode corresponder a uma regra regular fornecida.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'present' => 'O campo :attribute deve estar presente.',
    'prohibits' => 'O campo :attribute deve estar presente.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other for :value.',
    'required_unless' => 'O campo :attribute é obrigatório a menos que :other esteja em :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum de :values estiver presente.',
    'same' => 'Os campos :attribute e :other devem corresponder.',
    'size' => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file' => 'O campo :attribute deve ter :size kilobytes.',
        'string' => 'O campo :attribute deve ter :size caracteres.',
        'array' => 'O campo :attribute deve conter :size itens.',
    ],
    'starts_with' => 'O campo :attribute deve começar com :values',
    'string' => 'O campo :attribute deve ser uma string.',
    'timezone' => 'O campo :attribute deve ser uma zona válida.',
    'unique' => 'O :attribute já está em uso.',
    'uploaded' => 'Falha ao enviar o :attribute.',
    'uppercase' => 'O campo :attribute deve estar em maiúsculas.',
    'url' => 'O formato do campo :attribute é inválido.',
    'ulid' => 'O campo :attribute deve ser um ULID válido.',
    'uuid' => 'O :attribute é um UUID inválido.',
    'max_if' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max quando :other for :value.',
        'file' => 'O campo :attribute não pode ter mais de :max kilobytes quando :other for :value.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres quando :other for :value.',
        'array' => 'O campo :attribute não pode ter mais de :max itens quando :other for :value.',
    ],
    'min_if' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min quando :other for :value.',
        'file' => 'O campo :attribute deve ter no mínimo :min kilobytes quando :other for :value.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres quando :other for :value.',
        'array' => 'O campo :attribute deve ter no mínimo :min itens quando :other for :value.',
    ],
    'between_if' => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max quando :other for :value.',
        'file' => 'O campo :attribute deve ter entre :min e :max kilobytes quando :other for :value.',
        'string' => 'O campo :attribute deve ter entre :min e :max caracteres quando :other for :value.',
        'array' => 'O campo :attribute deve ter entre :min e :max itens quando :other for :value.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
    'phone_number' => 'O campo :attribute deve ser um número de telefone válido',
    'telephone_number' => 'O campo :attribute deve ser um número de telefone válido',

    'chinese_word' => 'O campo :attribute deve conter caracteres válidos (caracteres chineses/ingleses, números, sublinhados)',
    'sequential_array' => 'O campo :attribute deve ser um array sequencial',
];
