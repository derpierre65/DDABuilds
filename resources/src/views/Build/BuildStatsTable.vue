<template>
	<table v-if="editMode || heroStatsList.length" class="table table-transparent" :class="{'table-dark': $store.state.darkMode}">
		<thead>
			<tr>
				<td class="text-right" colspan="2">{{$t('build.heroStats.fortify')}}</td>
				<td class="text-right">{{$t('build.heroStats.power')}}</td>
				<td class="text-right">{{$t('build.heroStats.range')}}</td>
				<td class="text-right">{{$t('build.heroStats.defRate')}}</td>
			</tr>
		</thead>
		<tbody>
			<tr v-for="hero in heroStatsList" :key="hero.id">
				<td>
					<img v-b-tooltip="$t('hero.' + heroList[hero.id])" :alt="$t('hero.' + heroList[hero.id])" :src="'/assets/images/hero/' + heroList[hero.id] + '.png'"
						class="heroAttribute">
				</td>
				<td v-for="attribute in ['hp', 'damage', 'range', 'rate']" :key="attribute">
					<input v-if="editMode" v-model.number="hero[attribute]" class="form-control" min="0" size="5" type="text">
					<div v-else class="text-right">{{hero[attribute]}}</div>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
export default {
	name: 'BuildStatsTable',
	props: {
		editMode: Boolean,
		value: {
			type: Array,
			default() {
				return [];
			},
		},
		heroList: {
			type: Object,
			default() {
				return {};
			},
		},
	},
	data() {
		return {
			heroStats: this.value,
		};
	},
	computed: {
		heroStatsList() {
			if (this.editMode) {
				return this.heroStats;
			}

			return this.heroStats.filter((heroStat) => {
				for (const attribute of ['hp', 'range', 'damage', 'rate']) {
					if (heroStat[attribute] > 0) {
						return true;
					}
				}

				return false;
			});
		},
	},
	watch: {
		value(newValue) {
			this.heroStats = newValue;
		},
		heroStats(newValue) {
			this.$emit('input', newValue);
		},
	},
};
</script>