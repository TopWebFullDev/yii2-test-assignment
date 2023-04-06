<template>
  <div class="home">
    <h1>Your favorite fruits</h1>

    <table-lite
      :is-static-mode="false"
      :is-loading="table.isLoading"
      :columns="table.columns"
      :rows="table.rows"
      :total="table.totalRecordCount"
      :sortable="table.sortable"
      :pageSize="table.pageSize"
    ></table-lite>
  </div>
</template>

<script>
// @ is an alias to /src
import { defineComponent, reactive, ref, computed, watch } from "vue";
import axios from "axios";
import TableLite from "vue3-table-lite";

export default defineComponent({
  name: "Home",
  components: {
    TableLite,
  },
  setup() {
    const searchTerm = ref(""); // Search text

    const data = reactive({
      rows: [],
    });

    /**
     * Get server data request
     */
    const myRequest = async (name = "", family = "", perPage = 5, page = 1) => {
      try {
        table.isLoading = true;
        const response = await axios.get("http://localhost:8081/fruit", {
          params: {
            name,
            family,
            "per-page": perPage,
            page,
          },
        });

        table.isLoading = false;
        return response.data;
      } catch (error) {
        console.log("Fetch error", error);
      }
    };

    // Table config
    const table = reactive({
      isLoading: false,
      columns: [
        {
          label: "ID",
          field: "id",
        },
        {
          label: "Genus",
          field: "genus",
        },
        {
          label: "Name",
          field: "name",
          width: "100%",
        },
        {
          label: "Family",
          field: "family",
        },
        {
          label: "Order",
          field: "order",
        },
        {
          label: "Carbohydrates",
          field: "n_carbohydrates",
        },
        {
          label: "Protein",
          field: "n_protein",
        },
        {
          label: "Fat",
          field: "n_fat",
        },
        {
          label: "Calories",
          field: "n_calories",
        },
        {
          label: "Sugar",
          field: "n_sugar",
        },
      ],
      rows: computed(() => {
        return data.rows;
      }),
      pageSize: computed(() => {
        return 5;
      }),
      totalRecordCount: computed(() => {
        return table.rows.length;
      }),
      sortable: {
        order: "id",
        sort: "asc",
      },
    });

    watch(
      () => searchTerm.value,
      (val) => {
        myRequest(val).then((newData) => {
          data.page = newData?.page || {};
          data.rows = newData?.rows || [];
        });
      }
    );

    // Get data on first rendering
    myRequest("").then((newData) => {
      data.page = newData?.page || {};
      data.rows = newData?.rows || [];
    });

    return {
      searchTerm,
      table,
    };
  },
});
</script>
