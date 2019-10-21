<?php

?>
<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section id="trackmaniaResultList<?= $bId ?>" data-vue-array="trackmaniaResultList.ranks">
    <header>
        <h2>Live Trackmania Results</h2>
    </header>
    <div class="widget-table-overflow">
        <div class="body slimScroll">
            <table class="table table-striped table-lg ranking-table">
                <thead>
                <tr>
                    <th width="75">Rank</th>
                    <th width="50">Lap Time</th>
                    <th width="*">Nickname</th>
                </tr>
                </thead>
                <tbody name="ranking-list" is="transition-group">
                <template v-for="rank, index in ranks">
                    <tr :key="rank.user_id" class="ranking-list-item">
                        <td>{{ index }}&nbsp;
                            <span v-if="rank.rank_movement > 0" class="success">
                                <i class="fa fa-arrow-up"></i> {{ rank.rank_movement }}
                            </span>
                            <span v-if="rank.rank_movement < 0" class="danger">
                                <i class="fa fa-arrow-down"></i> {{ rank.rank_movement }}
                            </span>
                            <span v-else>

                            </span>
                        </td>
                        <td>{{ rank.lap_time }}</td>
                        <td><a data-toggle="modal" data-target="#modal" :data-source="rank.user_profile"
                               class="btn btn-round btn-outline-primary btn-sm"><i class="fa fa-user"></i> {{ rank.user
                                }}</a></td>
                    </tr>
                </template>
                </tbody>
            </table>
            <button class="btn btn-transparent" v-on:click="update">Update</button>
        </div>
    </div>
</section>


<script>
    /**
     * Feeed the news Feed with initial Data
     */
    var trackmaniaResultList = new Vue({
        el: '#trackmaniaResultList<?=$bId?>',
        data: {
            ranks: <?=$tmRankingList?>
        },
        methods: {
            update: function (event) {
                $.get("/tfts/api/trackmaniaRankingList", function (responseData) {
                    trackmaniaResultList.ranks = responseData;
                }).fail(function (response) {
                    Tfts.error(response.responseJSON.error.message);
                });
            }
        }
    });
    $(function(){
        var pusher = new Pusher('<?=Config::get('turicane.pusher.app_key')?>', {
            cluster: '<?=Config::get('turicane.pusher.app_cluster')?>'
        });

        var channel = pusher.subscribe('trackmaniaRankingList');
        channel.bind('update', function() {
            trackmaniaResultList.update();
        });
    });
</script>

<style>
    /** Set a few Styles to make beautiful animations when stuff changes */
    .ranking-list-item {
        transition: all 1s;
        margin-right: 10px;
    }

    .ranking-list-enter, .ranking-list-leave-to {
        opacity: 0;
        transform: translateY(30px);
    }

    .ranking-list-leave-active {
        position: absolute;
    }

    .ranking-list-move {
        transition: transform 1s;
    }
</style>