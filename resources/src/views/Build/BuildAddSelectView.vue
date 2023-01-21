<template>
	<div class="container">
		<div class="list-group text-center">
			<a
				v-for="mapCategory in mapCategories"
				:key="mapCategory.name"
				:href="`#category-${mapCategory.name}`"
				class="list-group-item list-group-item-action">
				<span>{{$t('mapCategory.' + mapCategory.name)}}</span>
			</a>
		</div>

		<div v-for="mapCategory in mapCategories" :id="`category-${mapCategory.name}`" :key="mapCategory.name">
			<h1 class="page-header">
				<span>{{$t('mapCategory.' + mapCategory.name)}}</span>
			</h1>
			<ol class="buildList mapSelection">
				<li v-for="map in mapCategory.maps" :key="map.id" class="pointer" @click="selectMap(map.routeParams)">
					<div class="buildBox">
						<div class="box128">
							<div class="buildDataContainer">
								<h4 class="buildSubject text-center">
									<router-link :to="{name: 'build-add', params: map.routeParams }">{{$t('map.' + map.name)}}</router-link>
								</h4>
								<router-link :to="{name: 'build-add', params: map.routeParams }">
									<img :src="'/assets/images/map/' + map.name + '.png'" alt="Missing Image" class="img-responsive" style="height: 200px; margin: 15px auto auto;">
								</router-link>
							</div>
						</div>
						<div class="buildFiller" />
					</div>
				</li>
			</ol>
		</div>
	</div>
</template>

<script>
import axios from 'axios';
import {hidePageLoader, showPageLoader} from '../../store';
import {formatSEOTitle, formatString} from '../../utils/string';

export default {
	name: 'BuildAddSelectView',
	data() {
		return {
			mapCategories: [],
		};
	},
	created() {
		showPageLoader();

		axios
			.get('/maps')
			.then(({ data }) => {
				for (const idx in data) {
					let mapCategory = data[idx];
					for (const map of mapCategory.maps || []) {
						map.routeParams = {
							mapId: map.id,
							name: formatSEOTitle(formatString(map.name, '-')),
						};
					}
				}

				this.mapCategories = data;
			})
			.catch(() => {
				this.$notify({
					type: 'error',
					text: this.$t('error.default'),
				});
				this.$router.push({name: 'home'});
			})
			.finally(hidePageLoader);
	},
	methods: {
		selectMap(params) {
			this.$router.push({ name: 'build-add', params });
		},
	},
};
</script>