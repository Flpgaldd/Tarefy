<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // 🎯 NOVO: validação da foto de perfil.
            // - image: garante que é mesmo uma imagem (não só a extensão do nome)
            // - mimes: só jpg/jpeg/png
            // - max:2048: limite de 2MB (valor em KB)
            // - dimensions: pelo menos 100x100px, pra evitar upload de miniatura
            //   ridiculamente pequena que fica pixelizada no círculo de 112px do perfil
            // 🎯 NOVO: validação do campo "Sobre você" (bio) do perfil.
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                'dimensions:min_width=100,min_height=100',
            ],
        ];
    }

    /**
     * 🎯 NOVO: mensagens de erro em português específicas pro campo de foto,
     * já que o resto do projeto usa o pacote laravel-lang/common.
     */
    public function messages(): array
    {
        return [
            'avatar.image' => 'O arquivo enviado precisa ser uma imagem.',
            'avatar.mimes' => 'A foto precisa estar em formato JPG ou PNG.',
            'avatar.max' => 'A foto não pode passar de 2MB.',
            'avatar.dimensions' => 'A foto precisa ter pelo menos 100x100 pixels.',
        ];
    }
}
