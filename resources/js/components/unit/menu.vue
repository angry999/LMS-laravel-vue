<template>
  <div class="unit-menu-container">
    <titlebar :back="routeTo('units')">{{ unit.title }}</titlebar>
    <preview :image="unit.preview">
      <div class="overlay">
        <router-link
          :to="routeTo('unit-listening', unit.id)"
          class="play icon-48 icon-play"
        ></router-link>
      </div>
    </preview>
    <div class="activities">
      <unit-activity
        v-for="activity in unit.activities"
        :key="activity.number"
        :activity="activity"
        :unit="unit"
      />
      <unit-activity-speech
        v-for="activity in unit.guidedspeeches"
        :key="activity.number + 100"
        :activity="activity"
        :unit="unit"
      />
    </div>
  </div>
</template>

<script>
import UnitMixin from "../../mixins/unit";

export default {
  mixins: [UnitMixin],
};
</script>
