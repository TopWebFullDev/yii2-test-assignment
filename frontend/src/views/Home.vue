<template>
  <div class="label-div">
    <label>Name:</label><input v-model="searchItem1" /> <label>Family:</label
    ><input v-model="searchItem2" />
    <button class="add-fruit" @click="onButtonClick($event)">Add Fruit</button>
  </div>

  <table-lite
    :is-static-mode="true"
    :is-loading="table.isLoading"
    :rows="table.rows"
    :columns="table.columns"
    :total="table.totalRecordCount"
  ></table-lite>
</template>

<script>
import { defineComponent, reactive, ref, computed, watch } from "vue";
import TableLite from "vue3-table-lite";
import axios from "axios";

const searchItem1 = ref("");
const searchItem2 = ref("");
const newFruit = {
  genus: "Persea",
  name: "Avocado",
  family: "Lauraceae",
  order: "Malvales",
  n_carbohydrates: 1.5,
  n_protein: 0.5,
  n_fat: 0.2,
  n_calories: 30,
  n_sugar: 6.5,
};

export default defineComponent({
  name: "App",
  components: { TableLite },
  methods: {
    onButtonClick() {
      const response = confirm("Do you want a new fruit?");

      if (response) {
        axios
          .post("http://localhost:8081/fruit/create", newFruit)
          .then((response) => {
            alert("Send a message for the new added fruit");
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        alert("It is cancelled");
      }
    },
  },
  setup() {
    const data = reactive({
      rows: [],
    });

    const searchData = async (name = "", family = "") => {
      return await new Promise((resolve, reject) => {
        try {
          table.isLoading = true;
          axios
            .get("http://localhost:8081/fruit", {
              params: {
                name,
                family,
              },
            })
            .then((res) => {
              table.isLoading = false;
              resolve(res.data);
            });
        } catch (error) {
          console.log("Fetch error", error);
          reject();
        }
      });
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
      totalRecordCount: computed(() => {
        return table.rows.length;
      }),
      sortable: {
        order: "id",
        sort: "asc",
      },
    });

    searchData().then((res) => {
      data.rows = res?.rows || [];
    });

    watch(
      () => [searchItem1.value, searchItem2.value],
      ([value, value2]) => {
        searchData(value, value2).then((res) => {
          data.rows = res?.rows || [];
        });
      }
    );

    return {
      searchItem1,
      searchItem2,
      table,
    };
  },
});
</script>

<style scope>
.label-div {
  margin: 20px;
  display: flex;
  grid-gap: 15px;
  align-items: center;
}
.add-fruit {
  margin-left: auto;
  color: #fff;
  background-color: #ca4820;
  border: none;
  border-radius: 5px;
  padding: 0.5rem 1rem;
}
</style>
