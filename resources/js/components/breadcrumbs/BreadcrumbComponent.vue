<template>
  <div>
    <v-breadcrumbs
      customDivider
      divider=">"
      style="
        background-color: rgba(0, 0, 0, 0.042);
        height: 70px;
        color: gray;
      "
    >
      <v-breadcrumbs-item
        v-for="(breadcrumb, idx) in breadcrumbList"
        :key="idx"
        @click="routeTo(idx)"
        :class="{ linked: !!breadcrumb.link }"
        style="
        font-size: 18px;
        font-weight: bold;
        font-family: 'Trebuchet MS';
      "
        >{{ breadcrumb.name }}</v-breadcrumbs-item
      >
    </v-breadcrumbs>
  </div>
</template>

<script>
export default {
  name: "BreadcrumbComponent",
  data() {
    return {
      breadcrumbList: [],
    };
  },
  mounted() {
    this.updateList();
  },
  watch: {
    $route() {
      this.updateList();
    },
  },
  methods: {
    routeTo(pRouteTo) {
      if (this.breadcrumbList[pRouteTo].link)
        this.$router.push(this.breadcrumbList[pRouteTo].link);
    },
    updateList() {
      this.breadcrumbList = this.$route.meta.breadcrumb;
    },
  },
};
</script>