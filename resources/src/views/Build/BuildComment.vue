<template>
	<div class="card panel-shadow">
		<div style="display:flex;">
			<div>
				<img :src="getSteamAvatar(comment.user_avatar)" alt="avatar profile" class="img-circle avatar">
			</div>
			<div style="margin-left: 15px;">
				<a :href="'https://steamcommunity.com/profiles/' + comment.user_id" target="_blank">{{comment.user_name}}</a><br>
				{{$t('')}} <!-- TODO workaround for locale switching -->
				<ul class="inlineList dotSeparated text-muted">
					<li><i class="fa fa-clock-o" /> {{formatDate(comment.created_at)}}</li>
					<li><i class="fa fa-thumbs-up" /> {{comment.likes}}</li>
					<li><i class="fa fa-thumbs-down" /> {{comment.dislikes}}</li>
				</ul>
			</div>
		</div>
		<div class="marginTop">
			<p class="user-content" v-html="comment.description" />
			<div v-if="canLike" class="marginTop">
				<button :class="['btn', {'btn-default': comment.likeValue !== like, 'btn-success': comment.likeValue === like}]" @click="vote(like)">
					<i class="fa fa-thumbs-up icon" /> {{comment.likes}}
				</button>
				<button :class="['btn', {'btn-default': comment.likeValue !== dislike, 'btn-danger': comment.likeValue === dislike}]" @click="vote(dislike)">
					<i class="fa fa-thumbs-down icon" /> {{comment.dislikes}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import {getSteamAvatar} from '../../utils/build';
import formatDate from '../../utils/date';
import {DISLIKE, LIKE, like} from '../../utils/like';

export default {
	name: 'BuildComment',
	props: {
		comment: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			like: LIKE,
			dislike: DISLIKE,
		};
	},
	computed: {
		canLike() {
			if (!this.$store.getters['authentication/isLoggedIn']) {
				return false;
			}

			return this.comment.user_id !== this.$store.state.authentication.user.id;
		},
	},
	methods: {
		getSteamAvatar,
		formatDate,
		vote(likeValue) {
			if (!this.canLike) {
				return;
			}

			like('comment', this.comment, likeValue);
		},
	},
};
</script>

<style>
.panel-shadow {
	border: 1px solid #DDDDDD;
	box-shadow: rgba(0, 0, 0, 0.3) 7px 7px 7px;
	padding: 15px;
}
</style>