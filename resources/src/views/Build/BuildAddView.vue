<template>
	<div class="container-fluid">
		<template v-if="errors.towers">
			<div v-for="(error, key) in errors.towers" :key="key" class="alert alert-danger">
				{{error}}
			</div>
		</template>

		<div class="tab-content">
			<!-- wave tab menu -->
			<ul class="nav nav-tabs">
				<li v-for="(waveName, key) of waveNames" :key="key" class="nav-item" @click="waveSelect(key)">
					<a :class="{active: selectedWave === key}" class="nav-link pointer">
						{{waveName}}
						<template v-if="isEditMode">
							<i class="fa fa-edit" @click.stop="waveEdit(key)" />
							<i v-if="key !== 0" class="fa fa-trash" @click.stop="waveDelete(key)" />
						</template>
					</a>
				</li>
				<li v-if="isEditMode" class="nav-item" @click="waveAdd">
					<a class="nav-link pointer">+</a>
				</li>
				<li v-if="build.id" class="nav-item" @click="waveSelect(-1)">
					<a :class="{active: selectedWave === -1}" class="nav-link pointer">{{$t('build.comments')}} (<span>{{build.comments}}</span>)</a>
				</li>
			</ul>
			<keep-alive>
				<build-comment-list v-if="selectedWave === -1" :build-id="build.id" @new-comment="build.comments ++" />
			</keep-alive>
			<template v-if="selectedWave !== -1">
				<div class="row">
					<div class="col-lg-9">
						<!-- map container -->
						<div id="mapContainer" ref="mapContainer">
							<img :src="`/assets/images/map/${map.name}.png`" alt="map image" class="buildMap">

							<div v-for="(entry, key) of waveTowersFiltered"
								:key="key"
								ref="placedTower"
								:data-class="entry.tower.hero_id"
								:style="{position: 'absolute', left: entry.placed.x + 'px', top: entry.placed.y + 'px', transform: 'rotate(' + entry.placed.rotation + 'deg)'}"
								:title="$t('tower.' + entry.tower.name) + (entry.tower.is_resizable ? ' (' + entry.placed.size + ')' : '')"
								class="tower-container pointer"
								@mouseout="towerMouseOut(entry.placed, key)"
								@mouseover.stop="towerMouseOver(entry.placed, key)"
								@contextmenu.prevent="towerDelete(entry.placed)">
								<img :alt="entry.tower.name" :src="`/assets/images/tower/${entry.tower.name}${entry.placed.size ? '_'+(entry.placed.size - entry.tower.unit_cost) : ''}.png`" class="tower" :style="getTowerStyle(entry)">
								<div v-if="isEditMode && mouseOver === key && (entry.tower.is_resizable || entry.tower.is_rotatable)" class="menu">
									<i v-if="entry.tower.is_resizable" class="fa fa-minus du-decrease" :style="entry.placed.size > entry.tower.unit_cost ? '' : 'opacity: 0.5'" @click="towerUpdateSize(entry.placed, -1)" />
									<i v-if="entry.tower.is_rotatable" class="fa fa-repeat" @mousedown="towerMouseDown(entry.placed, key)" />
									<i v-if="entry.tower.is_resizable" class="fa fa-plus du-increase" :style="entry.placed.size < entry.tower.max_unit_cost ? '' : 'opacity: 0.5'" @click="towerUpdateSize(entry.placed, 1)" />
								</div>
							</div>
						</div>
					</div>
					<!-- tower control panel -->
					<div id="towerControlPanel" class="col-lg-3 marginTop">
						<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header">
										<i v-if="canLike" :class="{'fa-star-o': !build.watch_status, 'fa-star': build.watch_status}" class="fa pointer text-warning"
											@click="buildWatch" />
										<i v-if="build.build_status !== buildStatusPublic" v-b-tooltip.hover="$t('build.isPrivate')" class="fa fa-eye-slash" />
										<span v-if="build.is_rifted" class="badge badge-success">{{$t('build.rifted')}}</span>
										<span v-if="build.is_afk_able" class="badge badge-success">{{$t('build.afkAble')}}</span>
										<span v-if="build.is_hardcore" class="badge badge-success">{{$t('build.hardcore')}}</span>
										<template v-if="build.title">
											{{build.title}}
										</template>
										<i v-else>{{$t('build.noTitle')}}</i>
									</div>
									<div class="card-body">
										<div class="card-text">
											<template v-if="isEditMode">
												<div class="form-group">
													<label for="buildName">{{$t('build.title')}}</label>
													<input id="buildName" v-model.trim="build.title" :class="{'is-invalid': errors.title}" :placeholder="$t('build.title')"
														class="form-control"
														maxlength="128"
														type="text">
													<template v-if="errors.title">
														<div v-for="(error, key) in errors.title" :key="key" class="invalid-feedback">
															{{error}}
														</div>
													</template>
												</div>
												<div class="form-group">
													<label for="buildAuthor">{{$t('build.author')}}</label>
													<input id="buildAuthor" v-model.trim="build.author" :class="{'is-invalid': errors.author}" :placeholder="$t('build.author')"
														class="form-control"
														maxlength="20"
														type="text">
													<template v-if="errors.author">
														<div v-for="(error, key) in errors.author" :key="key" class="invalid-feedback">
															{{error}}
														</div>
													</template>
												</div>

												<i class="fa fa-map" /> {{$t('map.' + map.name)}}<br>
												DU: <strong :style="{color: unitsUsed === unitsMax ? 'red': ''}">{{unitsUsed}}/{{unitsMax}}</strong><br>
												{{$t('build.manaUsed')}}: <strong>{{manaUsed}}</strong><br>
												{{$t('build.manaUpgradeUsed')}}: <strong>{{manaUpgrade}}</strong><br>
											</template>
											<template v-else>
												<ul>
													<li><i class="fa fa-map" /> {{$t('map.' + map.name)}}</li>
													<li v-if="build.author">
														<i class="fa fa-user" /> <router-link :to="authorLink">{{build.author}}</router-link>
													</li>
													<li v-if="build.gameModeName">
														<i class="fa fa-gamepad" /> {{$t('gameMode.' + build.gameModeName)}}
													</li>
													<li v-if="build.date">
														<i class="fa fa-clock-o" /> {{formatDate(build.created_at)}}
													</li>
													<li v-if="build.exp_per_run">
														{{$t('build.expPerRun')}}: {{build.exp_per_run}}
													</li>
													<li v-if="build.time_per_run">
														{{$t('build.timePerRun')}}: {{build.time_per_run}}
													</li>
													<li>{{$t('build.manaUsed')}}: {{manaUsed}}</li>
													<li>{{$t('build.manaUpgradeUsed')}}: {{manaUpgrade}}</li>
													<li>DU: <strong><span>{{unitsUsed}}</span>/<span>{{unitsMax}}</span></strong></li>
												</ul>

												<build-stats-table v-model="build.hero_stats" :hero-list="heroList" />

												<button v-if="build.id" :class="['btn', {'btn-default': !build.likeValue, 'btn-success': build.likeValue, disabled: !canLike}]"
													:disabled="!canLike" @click="buildLike">
													<i class="fa fa-thumbs-up" /> {{build.likes}}
												</button>
											</template>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header">
										<i v-b-tooltip="$t('build.disableTowersHint')" class="fa fa-question-circle" /> {{$t('build.disableTowers')}}
									</div>
									<div class="card-body">
										<div class="card-text">
											<img v-for="hero in heros" :key="hero.id" v-b-tooltip.hover="$t('hero.' + hero.name)"
												:class="{disabled: disabledHeros.includes(hero.id)}"
												:src="'/assets/images/hero/' + hero.name + '.png'" class="disableTowerCheckbox" @click="toggleHeroClass(hero.id)">
										</div>
									</div>
								</div>
							</div>
							<template v-if="isEditMode">
								<div v-for="hero in heroPanels" :key="hero.id" :class="{'col-sm-6': hero.towers.length < 8, 'col-sm-12': hero.towers.length >= 8}">
									<div class="card">
										<div class="card-header">
											{{$t('hero.' + hero.name)}}
										</div>
										<div class="card-body card-hero-body">
											<div v-for="tower of hero.towers"
												:key="tower.id"
												:class="{disabled: unitsUsed + tower.unit_cost > unitsMax}"
												:data-class="hero.id"
												:data-tower="tower.id"
												class="tower-container pointer dummy">
												<img v-b-tooltip.hover="`${$t(`tower.${tower.name}`)} (${tower.unit_cost})`" :src="`/assets/images/tower/${tower.name}.png`" class="tower">
											</div>
										</div>
									</div>
								</div>

								<div class="col-sm-12">
									<div class="card">
										<div class="card-header">
											{{$t('build.details')}}
										</div>
										<div class="card-body">
											<!-- build status -->
											<div class="form-group">
												<label for="buildStatus">{{$t('build.buildStatus')}}</label>
												<select id="buildStatus" v-model.number="build.build_status" class="form-control">
													<option v-for="i in 3" :key="i" :value="i">
														{{$t('build.status.' + (i - 1))}}
													</option>
												</select>
											</div>

											<!-- difficulty -->
											<div class="form-group">
												<label for="difficulty">{{$t('build.difficulty')}}</label>
												<select id="difficulty" v-model="build.difficulty_id" :class="`difficulty-${build.difficulty_id}`" class="form-control">
													<option v-for="difficulty in difficulties" :key="difficulty.id" :class="'difficulty-' + difficulty.id" :value="difficulty.id">
														{{$t('difficulty.' + difficulty.name)}}
													</option>
												</select>
											</div>
											<div class="form-group">
												<label :class="{'is-invalid': errors.game_mode_id}">{{$t('build.gameMode')}}</label>
												<br>
												<div v-for="gameMode in gameModes" :key="gameMode.id" class="form-check form-check-inline">
													<input :id="'buildGameMode' + gameMode.id" v-model="build.game_mode_id" :value="gameMode.id" class="form-check-input"
														type="radio">
													<label :for="'buildGameMode' + gameMode.id" class="form-check-label">{{$t('gameMode.' + gameMode.name)}}</label>
												</div>
												<template v-if="errors.game_mode_id">
													<div v-for="(error, key) in errors.game_mode_id" :key="key" class="invalid-feedback">
														{{error}}
													</div>
												</template>
											</div>

											<div class="form-group">
												<label>{{$t('build.modifiers')}}</label>
												<br>
												<div class="form-check">
													<input id="buildHardcore" v-model="build.is_hardcore" class="form-check-input" type="checkbox">
													<label class="form-check-label" for="buildHardcore"> {{$t('build.hardcore')}}</label>
												</div>
												<div class="form-check">
													<input id="buildAFKAble" v-model="build.is_afk_able" class="form-check-input" type="checkbox">
													<label class="form-check-label" for="buildAFKAble"> {{$t('build.afkAble')}}</label>
												</div>
												<div class="form-check">
													<input id="buildRifted" v-model="build.is_rifted" class="form-check-input" type="checkbox">
													<label class="form-check-label" for="buildRifted"> {{$t('build.rifted')}}</label>
												</div>
											</div>

											<div class="form-group">
												<label for="buildExpPerRun">{{$t('build.expPerRun')}}</label>
												<input id="buildExpPerRun" v-model="build.exp_per_run" :placeholder="$t('build.expPerRun')" class="form-control" maxlength="20"
													type="text">
											</div>
											<div class="form-group">
												<label for="buildTimePerRun">{{$t('build.timePerRun')}}</label>
												<input id="buildTimePerRun" v-model="build.time_per_run" :placeholder="$t('build.timePerRun')" class="form-control" maxlength="20"
													type="text">
											</div>

											<div class="form-group">
												<h5 class="text-center">
													{{$t('build.requiredAttributes')}}
												</h5>

												<build-stats-table v-model="build.hero_stats" :hero-list="heroList" edit-mode />
											</div>
										</div>
									</div>
								</div>
							</template>
						</div>
					</div>
				</div>

				<div v-if="isEditMode || build.description" class="container">
					<div class="card">
						<div class="card-header">
							{{$t('build.description')}}
						</div>
						<div v-if="isEditMode" class="card-body">
							<classic-ckeditor v-model="build.description" />
						</div>
						<div v-else class="card-body user-content" v-html="build.description" />
					</div>
				</div>
			</template>
		</div>

		<div v-if="canEdit" style="position:fixed;bottom:20px;z-index:10;display:flex;justify-content:center;width:100%;">
			<template v-if="isEditMode">
				<button class="btn btn-primary" @click="save">
					{{$t('words.save')}}
				</button>
				<button class="btn btn-secondary" @click="buildChangeMode(true)">
					{{$t('build.viewerMode')}}
				</button>
				<button v-if="build.id" class="btn btn-danger" @click="buildDelete">
					{{$t('build.delete')}}
				</button>
			</template>
			<button v-else class="btn btn-secondary" @click="buildChangeMode(false)">
				{{$t('build.editorMode')}}
			</button>
		</div>
	</div>
</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import Vue from 'vue';
import ClassicCkeditor from '../../components/ClassicCkeditor';
import {hideAjaxLoader, hidePageLoader, showAjaxLoader, showPageLoader} from '../../store';
import {buildListSearch, STATUS_PUBLIC} from '../../utils/build';
import formatDate from '../../utils/date';
import {LIKE, like} from '../../utils/like';
import {formatSEOTitle} from '../../utils/string';
import BuildCommentList from './BuildCommentList';
import BuildStatsTable from './BuildStatsTable';

window.$ = $;

export default {
	name: 'BuildAddView',
	components: { BuildCommentList, BuildStatsTable, ClassicCkeditor },
	props: {
		isView: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			buildStatusPublic: STATUS_PUBLIC,
			build: {
				title: '',
				author: this.$store.state.authentication.user.name,
				description: '',
				difficulty_id: 1,
				game_mode_id: 0,
				build_status: 1,
				time_per_run: '',
				exp_per_run: '',
				hero_stats: [],
				is_hardcore: false,
				is_afk_able: false,
				is_rifted: false,
			},
			selectedWave: 0,
			placedTowers: [],
			units: {},
			disabledHeros: [],
			map: {},
			gameModes: [],
			heros: [],
			waveNames: ['Build'],
			towers: {},
			difficulties: [],
			errors: {},
			fetching: 0,
			loaded: false,
			demoMode: false,
			rotateTower: false,
			mouseOver: null,
		};
	},
	computed: {
		heroPanels() {
			return this.heros.filter((hero) => hero.towers.length > 0);
		},
		authorLink() {
			return {
				name: 'buildList',
				query: buildListSearch({author: this.build.author}),
			};
		},
		canLike() {
			if (!this.$store.getters['authentication/isLoggedIn']) {
				return false;
			}

			return !this.canEdit;
		},
		canEdit() {
			return !this.build.id || this.build.user_id === this.$store.state.authentication.user.id;
		},
		isEditMode() {
			if (this.demoMode) {
				return false;
			}

			return !this.isView || this.canEdit;
		},
		waveTowers() {
			let towers = [];
			for (const tower of this.placedTowers) {
				if (tower.wave_id === this.selectedWave) {
					towers.push(tower);
				}
			}

			return towers;
		},
		waveTowersFiltered() {
			let towers = [];
			for (const placed of this.waveTowers) {
				let tower = this.towers[placed.id];
				if (!this.disabledHeros.includes(tower.hero_id)) {
					towers.push({
						placed: placed,
						tower,
					});
				}
			}

			return towers;
		},
		manaUpgrade() {
			return this.waveTowers.length * 2620;
		},
		manaUsed() {
			let manaUsed = 0;
			for (const tower of this.waveTowers) {
				manaUsed += this.towers[tower.id].mana;
			}

			return manaUsed;
		},
		unitsUsed() {
			let units = 0;
			for (const tower of this.waveTowers) {
				let unitCount = tower.size ? tower.size : this.towers[tower.id].unit_cost;
				units += unitCount;
			}

			return units;
		},
		unitsMax() {
			for (const difficulty of this.map.difficulty_units || []) {
				if (this.build.difficulty_id === difficulty.difficulty_id) {
					return difficulty.units;
				}
			}

			return this.map.units || 0;
		},
		heroList() {
			const heroes = {};
			for (const hero of this.heros) {
				if (hero.is_hero) {
					heroes[hero.id] = hero.name;
				}
			}

			return heroes;
		},
	},
	watch: {
		'$route.params.id'() {
			this.fetch();
		},
		'$route.params.mapId'() {
			this.fetch();
		},
		selectedWave(newValue, oldValue) {
			if (oldValue === -1) {
				this.$nextTick(() => {
					this.startDroppable();
					this.startDraggable();
				});
			}
		},
		waveTowersFiltered() {
			this.$nextTick(() => {
				for (let key in this.$refs.placedTower) {
					let el = this.$refs.placedTower[key];
					$(el).draggable({
						containment: '#mapContainer',
						stop: (event, ui) => {
							let offset = $(this.$refs.mapContainer).offset();

							this.placedTowers[key].x = ui.offset.left - offset.left;
							this.placedTowers[key].y = ui.offset.top - offset.top;
						},
					});
				}
			});
		},
	},
	mounted() {
		this.startDroppable();
	},
	created() {
		this.fetch();
	},
	methods: {
		formatDate,
		startDroppable() {
			let canvas = $('#mapContainer');
			canvas.droppable({
				accept: '.tower-container',
				drop: (event, ui) => {
					if (!$(ui.helper).hasClass('dummy')) {
						return;
					}

					let towerID = ui.helper.data('tower');
					let tower = this.towers[towerID];
					let offset = canvas.offset();

					this.placedTowers.push({
						id: towerID,
						wave_id: this.selectedWave,
						size: tower.unit_cost < tower.max_unit_cost ? tower.unit_cost : 0,
						x: ui.offset.left - offset.left,
						y: ui.offset.top - offset.top,
						rotation: 0,
					});
				},
			});
		},
		fetch() {
			if (this.fetching === this.$route.params.id) {
				return;
			}

			this.fetching = this.$route.params.id;

			showPageLoader();

			this.selectedWave = 0;
			let loading;
			if (!this.isView) {
				loading = this.fetchMap(this.$route.params.mapId, this.build);
			}
			else {
				loading = axios
					.get(`/builds/${this.$route.params.id}/`)
					.then(async ({ data: data }) => {
						// redirect to correct title url, if title not equal to the url title
						let title = formatSEOTitle(data.title);
						if (this.$route.params.title !== title) {
							this.$router.push({
								name: this.$route.name,
								params: Object.assign({}, this.$route.params, { title }),
							});
						}

						// fetch map data
						await this.fetchMap(data.map_id, data);

						const towers = [];
						const waveNames = [];
						let buildWaveId = 0;
						for (const wave of data.waves) {
							waveNames.push(wave.name);
							for (const tower of wave.towers) {
								towers.push({
									id: tower.id,
									wave_id: buildWaveId,
									x: tower.x,
									y: tower.y,
									rotation: tower.rotation,
									size: tower.size,
								});
							}

							buildWaveId++;
						}

						this.waveNames = waveNames;
						this.build = data;
						this.placedTowers = towers;
					})
					.catch((error) => {
						if (error.status === 403) {
							this.$notify({
								type: 'error',
								text: this.$t('error.403'),
							});
						}
						else if (error.status === 404) {
							this.$notify({
								type: 'error',
								text: this.$t('build.error.404'),
							});
						}

						if (window.history.length > 1) {
							this.$router.go(-1);
						}
						else {
							this.$router.push({ name: 'buildList' });
						}
					});
			}

			loading.then(() => {
				this.startDraggable();
			});
			loading.finally(hidePageLoader);
		},
		fetchMap(mapId, build) {
			return axios
				.get(`/maps/editor/${mapId}/`)
				.then(({ data }) => {
					const towers = {};
					for (const hero of data.heroes) {
						if (hero.is_hero && !build.hero_stats.find((stat) => stat.id === hero.id)) {
							build.hero_stats.push({
								id: hero.id,
								hp: 0,
								damage: 0,
								range: 0,
								rate: 0,
							});
						}

						for (const tower of hero.towers) {
							towers[tower.id] = tower;
						}
					}

					this.map = data.map;
					this.heros = data.heroes;
					this.difficulties = data.difficulties;
					this.gameModes = data.gameModes;
					this.towers = towers;
				})
				.catch(() => {
					this.$router.push({ name: 'home' });
				});
		},
		towerMouseOver(tower, key) {
			if (!this.towers[tower.id].is_rotatable || this.rotateTower || !this.isEditMode) {
				return;
			}

			if (tower.mouseoverTimeout) {
				window.clearTimeout(tower.mouseoverTimeout);
			}

			this.mouseOver = key;

			let defense = $(this.$refs.placedTower[key]);
			defense.on('wheel', (event) => {
				event.preventDefault();

				let scrollSpeed = 3 * (event.originalEvent.deltaY <= 0 ? -1 : 1);
				let delta = 3;
				if (event.shiftKey) {
					delta /= 2;
				}
				else if (event.ctrlKey) {
					delta *= 3;
				}

				tower.rotation += (scrollSpeed * delta);
				if (tower.rotation > 360) {
					tower.rotation -= 360;
				}
				else if (tower.rotation < 0) {
					tower.rotation += 360;
				}
			});
		},
		mousemove(event) {
			if (!this.rotateTower) {
				return;
			}

			let placed = this.waveTowersFiltered[this.rotateTower - 1]?.placed;
			if (placed && typeof placed.mousemove === 'function') {
				placed.mousemove(event);
			}
		},
		mouseup() {
			window.removeEventListener('mouseup', this.mouseup);
			window.addEventListener('mousemove', this.mousemove);

			$(this.$refs.placedTower[this.rotateTower - 1]).draggable('enable');
			this.rotateTower = 0;
		},
		getTowerStyle(entry) {
			const style = {};
			if ( typeof entry.tower.image_size === 'string' && entry.tower.image_size.length ) {
				const split = entry.tower.image_size.split('x');
				style.width = split[0] + 'px';
				style.height = split[1] + 'px';
			}

			return style;
		},
		towerMouseDown(tower, key) {
			this.rotateTower = key + 1;

			window.addEventListener('mouseup', this.mouseup);
			window.addEventListener('mousemove', this.mousemove);

			let el = this.$refs.placedTower[key];
			let rect = el.getBoundingClientRect();
			let offset = {
				top: rect.top + (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0),
				left: rect.left + document.body.scrollLeft,
			};
			let style = getComputedStyle(el, null);
			let width = parseFloat(style.width.replace('px', '')) / 2;
			let height = parseFloat(style.height.replace('px', '')) / 2;

			$(el).draggable('disable');
			tower.mousemove = (e) => {
				let mouse_x = e.pageX - offset.left - width;
				let mouse_y = e.pageY - offset.top - height;
				let mouse_cur_angle = Math.atan2(mouse_y, mouse_x);
				let rotation = mouse_cur_angle * (180 / Math.PI) + 90;
				if ( rotation < 0 ) {
					rotation += 360;
				}
				tower.rotation = rotation;
			};
		},
		towerMouseOut(tower, key) {
			if (tower.mouseoverTimeout) {
				window.clearTimeout(tower.mouseoverTimeout);
			}

			tower.mouseoverTimeout = window.setTimeout(() => {
				this.mouseOver = null;
				$(this.$refs.placedTower[key]).off('wheel');
			}, 50);
		},
		towerUpdateSize(tower, update) {
			const newTowerSize = tower.size + update;
			const towerInfo = this.towers[tower.id];
			if (newTowerSize < towerInfo.unit_cost || newTowerSize > towerInfo.max_unit_cost) {
				return;
			}

			tower.size += update;
		},
		towerDelete(tower) {
			this.placedTowers.splice(this.placedTowers.indexOf(tower), 1);
		},
		toggleHeroClass(heroId) {
			let idx = this.disabledHeros.indexOf(heroId);
			if (idx >= 0) {
				this.disabledHeros.splice(idx, 1);
			}
			else {
				this.disabledHeros.push(heroId);
			}
		},
		startDraggable() {
			$('#towerControlPanel .tower-container').draggable({
				helper: (event) => {
					const target = event.currentTarget;
					const towerId = target.getAttribute('data-tower');
					const tower = this.towers[towerId];
					const towerStyle = this.getTowerStyle({ tower });
					let towerImage = `/assets/images/tower/${tower.name}.png`;

					if (tower.is_resizable) {
						towerImage = towerImage.replace('.png', '_0.png');
					}

					let style = '';
					for ( const key of Object.keys(towerStyle) ) {
						style += `${key}:${towerStyle[key]};`;
					}

					return $(`<div data-class="${target.getAttribute('data-class')}" data-tower="${towerId}" class="tower-container pointer dummy ui-draggable-dragging"><img src="${towerImage}" class="tower" style="${style}"></div>`);
				},
				start: (event, ui) => {
					let instance = $(event.target).draggable('instance');
					let element = instance.element;
					if (element.hasClass('disabled')) {
						return false;
					}

					// center icon to the cursor
					function updatePosition() {
						instance.offset.click = {
							left: Math.floor(ui.helper.width() * 0.5),
							top: Math.floor(ui.helper.height() * 0.5),
						};
					}

					ui.helper.find('.tower')[0].onload = updatePosition;
					updatePosition();
				},
			});
		},
		waveAdd() {
			this.selectedWave = this.waveNames.push('custom wave') - 1;
		},
		waveSelect(waveId) {
			if (waveId <= this.waveNames.length + 1) {
				this.selectedWave = waveId;
			}
		},
		waveEdit(waveId) {
			let newWaveName = window.prompt(this.$t('build.promptWaveName', { count: 24 }));
			if (typeof newWaveName === 'string') {
				newWaveName = newWaveName.trim().substr(0, 24);
				if (newWaveName) {
					Vue.set(this.waveNames, waveId, newWaveName);
				}
			}
		},
		waveDelete(waveId) {
			for (const tower of this.placedTowers) {
				if (tower.wave_id === waveId) {
					if (!window.confirm(this.$t('build.wantWaveDelete'))) {
						return;
					}
					break;
				}
			}

			this.waveNames.splice(waveId, 1);
			for (const tower of this.placedTowers) {
				if (tower.wave_id === waveId) {
					this.placedTowers.splice(this.placedTowers.indexOf(tower), 1);
				}
				else if (tower.wave_id >= waveId) {
					tower.wave_id--;
				}
			}

			if (this.selectedWave === waveId) {
				this.selectedWave--;
			}
		},
		buildChangeMode(newMode) {
			this.demoMode = newMode;
			window.scrollTo(0, 0);

			if (!newMode) {
				this.$nextTick(this.startDraggable);
			}
		},
		buildWatch() {
			showAjaxLoader();

			axios
				.post(`/builds/${this.build.id}/watch/`)
				.then(({ data }) => this.build.watch_status = data.watch_status)
				.finally(hideAjaxLoader);
		},
		buildLike() {
			like('build', this.build, LIKE);
		},
		buildDelete() {
			if (!window.confirm(this.$t('build.deleteSure'))) {
				return;
			}

			showAjaxLoader();

			axios
				.delete(`/builds/${this.build.id}/`)
				.then(() => this.$router.push({ name: 'home' }))
				.catch(() => this.$notify({
					type: 'error',
					text: this.$t('error.default'),
				}))
				.finally(hideAjaxLoader);
		},
		save() {
			showAjaxLoader();

			// reset errors
			this.errors = {};

			// build data
			let build = {
				...this.build,
				waves: this.waveNames,
			};
			build.towers = this.placedTowers;
			build.map_id = this.map.id;

			// send xhr
			let request;
			if (this.build.id) {
				request = axios.patch(`/builds/${this.build.id}/`, build);
			}
			else {
				request = axios.post('/builds/', build);
			}

			request
				.then(({ data }) => {
					// redirect to build from add page
					if (!this.build.id) {
						this.$router.push({
							name: 'build',
							params: {
								id: data.id,
								title: formatSEOTitle(data.title),
							},
						});
					}

					this.$notify({
						type: 'success',
						text: this.$t('build.saved'),
					});
				})
				.catch((response) => {
					// try to find the status from error
					try {
						if (response.status === 422) {
							this.errors = response.errors;

							this.$notify({
								type: 'error',
								text: this.$t('error.invalidInput'),
							});
							window.scrollTo(0, 0);

							return;
						}
					}
					catch (e) {
						// do nothing
					}

					this.$notify({
						type: 'error',
						text: this.$t('error.default'),
					});
				})
				.finally(hideAjaxLoader);
		},
	},
};
</script>

<style lang="scss">
.tower-container {
	position: relative;
	display: inline-block;
	z-index: 10;

	&.disabled {
		opacity: .5;
		cursor: not-allowed;
	}

	.menu {
		height: 20px;
		z-index: 20;
		cursor: pointer;
		position: absolute;
		top: -20px;
		width: 100%;
		left: 11px;
		transition: 0.2s 1s;

		i {
			text-shadow: 0 0 3px #000;
		}
	}
}

.tower {
	width: 35px;
	height: 35px;
}

.buildMap,
.tower-container img {
	-webkit-user-select: none; /* Chrome all / Safari all */
	-moz-user-select: none; /* Firefox all */
	-ms-user-select: none; /* IE 10+ */
	user-select: none; /* Likely future */
}

// series ev
.tower-container[data-class="5"].ui-draggable-dragging img.tower,
#mapContainer .tower-container[data-class="5"] img.tower {
	width: auto;
	height: auto;
}

/* traps */
.tower-container[data-class="3"].ui-draggable-dragging img.tower,
#mapContainer .tower-container[data-class="3"] img.tower {
	width: 45px;
	height: 45px;
	line-height: 45px;
}

/* monk aura */
.tower-container[data-class="4"].ui-draggable-dragging img.tower,
#mapContainer .tower-container[data-class="4"] img.tower {
	width: 100px;
	height: 100px;
	line-height: 100px;
	opacity: 0.8;
}

#mapContainer {
	top: 30px;
	position: sticky;
	width: 1024px;
	height: 1024px;

	.buildMap {
		width: 100%;
	}
}

.disableTowerCheckbox {
	width: 48px;
	height: 48px;
	line-height: 64px;
	cursor: pointer;
}

.heroAttribute {
	width: 32px;
	height: 32px;
}

.disableTowerCheckbox.disabled {
	opacity: 0.2;
}

.card-hero-body {
	padding: 10px;
}
</style>