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
			'heroStatsIDs' => array_keys($this->get('heroStats', [])),
			'description' => $this->get('description', '') ?? '',
		]);
	}

	public function messages() : array
	{
		return [
			'gameModeID.exists' => __('build.gameModeID.exists'),
			'towers.required' => __('build.towers.required'),
		];
	}

	public function rules() : array
	{
		return [
			'title' => 'required|min:3|max:128',
			'description' => 'nullable',
			'author' => 'required|min:3|max:20',
			'timePerRun' => 'nullable|min:1|max:20',
			'expPerRun' => 'nullable|min:1|max:20',
			'afkAble' => 'nullable|boolean',
			'hardcore' => 'nullable|boolean',
			'rifted' => 'nullable|boolean',
			'gameModeID' => 'exists:game_modes,ID',
			'difficultyID' => 'exists:difficulties,ID',
			'buildStatus' => ['required', Rule::in([Build::STATUS_PRIVATE, Build::STATUS_PUBLIC, Build::STATUS_UNLISTED])],
			'mapID' => 'exists:maps,ID',
			'towers' => 'required|array|min:1',
			'towers.*.ID' => 'required|numeric|exists:towers,ID',
			'towers.*.x' => 'required|numeric|min:0|max:1024',
			'towers.*.y' => 'required|numeric|min:0|max:1024',
			'towers.*.rotation' => 'required|numeric|min:0|max:360',
			'towers.*.size' => 'required|numeric|min:0',
			'towers.*.waveID' => 'required|numeric|min:0',
			'heroStats' => 'nullable|array',
			'heroStatsIDs' => (new Exists('heroes', 'ID'))->where('isHero', 1),
			'heroStats.*.hp' => 'nullable|numeric',
			'heroStats.*.rate' => 'nullable|numeric',
			'heroStats.*.damage' => 'nullable|numeric',
			'heroStats.*.range' => 'nullable|numeric',
			'waves.*' => 'required|string|min:1|max:24',
		];
	}
}
