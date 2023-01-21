<?php

namespace App\Http\Requests;

use App\Models\Build;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class BuildRequest extends FormRequest
{
	public function prepareForValidation()
	{
		$this->merge([
			'description' => $this->get('description', '') ?? '',
		]);
	}

	public function messages() : array
	{
		return [
			'game_mode_id.exists' => __('build.game_mode_id.exists'),
			'towers.required' => __('build.towers.required'),
		];
	}

	public function rules() : array
	{
		return [
			'title' => 'required|min:3|max:128',
			'description' => 'nullable',
			'author' => 'required|min:3|max:20',
			'time_per_run' => 'nullable|min:1|max:20',
			'exp_per_run' => 'nullable|min:1|max:20',
			'is_afk_able' => 'nullable|boolean',
			'is_hardcore' => 'nullable|boolean',
			'is_rifted' => 'nullable|boolean',
			'game_mode_id' => 'exists:game_modes,ID',
			'difficulty_id' => 'exists:difficulties,ID',
			'build_status' => ['required', Rule::in([Build::STATUS_PRIVATE, Build::STATUS_PUBLIC, Build::STATUS_UNLISTED])],
			'map_id' => 'exists:maps,ID',
			'towers' => 'required|array|min:1',
			'towers.*.id' => 'required|numeric|exists:towers,id',
			'towers.*.x' => 'required|numeric|min:0|max:1024',
			'towers.*.y' => 'required|numeric|min:0|max:1024',
			'towers.*.rotation' => 'required|numeric|min:0|max:360',
			'towers.*.size' => 'required|numeric|min:0',
			'towers.*.wave_id' => 'required|numeric|min:0',
			'hero_stats' => 'nullable|array',
			'hero_stats.*.id' => ['nullable', (new Exists('heroes', 'id'))->where('is_hero', 1)],
			'hero_stats.*.hp' => 'nullable|numeric',
			'hero_stats.*.rate' => 'nullable|numeric',
			'hero_stats.*.damage' => 'nullable|numeric',
			'hero_stats.*.range' => 'nullable|numeric',
			'waves.*' => 'required|string|min:1|max:24',
		];
	}
}
