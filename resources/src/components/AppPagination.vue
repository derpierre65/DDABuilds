<template>
	<ul v-if="pages > 1" class="pagination justify-content-center flex-wrap marginTop">
		<li v-for="page in availablePages" :key="page.number" :class="{active: !page.to}" class="page-item">
			<router-link v-if="page.to" :to="page.to" class="page-link">{{page.number}}</router-link>
			<span v-else class="page-link">{{page.number}}</span>
		</li>
	</ul>
</template>

<script>
export default {
	name: 'AppPagination',
	props: {
		pages: {
			type: Number,
			default: 1,
			required: true,
			validator(value) {
				return value >= 0;
			},
		},
		currentPage: {
			type: Number,
			default: 1,
			validator(value) {
				return value >= 0;
			},
		},
		routeName: {
			type: String,
			default() {
				return this.$route.name;
			},
		},
		routeParams: {
			type: Object,
			default() {
				return {};
			},
		},
	},
	computed: {
		availablePages() {
			let pages = [];
			for (let i = 0; i < this.pages; i++) {
				let pageNumber = i + 1;
				pages.push({
					number: pageNumber,
					to: pageNumber !== this.currentPage ? {
						name: this.routeName,
						query: this.$route.query,
						params: Object.assign({}, this.routeParams, {
							page: pageNumber,
						}),
					} : null,
				});
			}

			return pages;
		},
	},
};
</script>