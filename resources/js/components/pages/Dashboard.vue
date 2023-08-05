<template>
  <main>
    <header>
      <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
          <span class="navbar-brand mb-0 h1"> Agence </span>
        </div>
      </nav>
    </header>

    <div class="container mt-5">
      <div
        class="row border p-3 bg-light align-items-center justify-content-center shadow"
      >
        <div class="col-4">
          <Card>
            <template #header>
              Consultores
            </template>
            <template #body>
              <ul class="list-group">
                <li
                    class="list-group-item"
                    v-for="(user, index) in users"
                    :key="user.id"
                    @click.prevent="selectUser(user, index)"
                >
                  {{ user.no_usuario }}
                </li>
              </ul>
            </template>
          </Card>
        </div>

        <div class="col-4">
          <Card>
            <template #header>
              Consultores Seleccionados
            </template>

            <template #body>
              <ul class="list-group">
                <li
                    class="list-group-item"
                    v-for="(selectedUser, index) in selectedUsers"
                    :key="selectedUser.id"
                    @click.prevent="unselectUser(selectedUser, index)"
                >
                  {{ selectedUser.no_usuario }}
                </li>
              </ul>
            </template>
          </Card>
        </div>

        <div class="col-4">
          <Card>
            <template #header>Acciones</template>
            <template #body>
              <div class="d-flex flex-column">
                <LoadingButton :type="'primary'" :disabled="isLoading" @click.prevent="fetchRelatorios()">
                  Relatorio
                </LoadingButton>

                <LoadingButton :type="'success'" :disabled="isLoading" @click.prevent="createBarChart()">
                  Grafico
                </LoadingButton>

                <LoadingButton :type="'info'" :disabled="isLoading" @click.prevent="createPizzaChart()">
                  Pizza
                </LoadingButton>
              </div>
            </template>
          </Card>
        </div>
      </div>

      <div class="row" id="relatorioSection">
        <div
          class="col-12 border p-3 bg-light align-items-center justify-content-center shadow mt-5"
          v-for="(consultant, index) in relatorios"
          :key="index"
        >
          <Card>
            <template #header>
              {{ index }}
            </template>

            <template #body>
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Periodo</th>
                  <th>Receita Liquida</th>
                  <th>Custo Fixo</th>
                  <th>Comissao</th>
                  <th>Lucro</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(relatorio, index) in consultant" :key="index">
                  <td>
                    {{ `${relatorio.date_emissao}` }}
                  </td>
                  <td>
                    {{ relatorio.receita_liquida }}
                  </td>
                  <td>
                    {{ relatorio.custo_fixo }}
                  </td>
                  <td>
                    {{ relatorio.comissao }}
                  </td>
                  <td>
                    {{ relatorio.lucro }}
                  </td>
                </tr>
                </tbody>
              </table>
            </template>
          </Card>
        </div>
      </div>
    </div>

    <div class="row align-items-center justify-content-center" id="chartSection">
      <div
        class="col-6 border p-3 bg-light shadow mt-5"
        v-show="showBarChart"
      >
        <Card>
          <template #header>
            {{ chart.data.title }}
          </template>

          <template #body>
            <Bar :data="chart.data" :options="chart.options" />
          </template>
        </Card>
      </div>

      <div
          class="col-6 border p-3 bg-light shadow mt-5"
          v-show="showPizzaChart"
      >
        <Card>
          <template #body>
            <Pie :data="pizza.data" />
          </template>
        </Card>
      </div>
    </div>
  </main>
</template>

<style>
ul li {
  cursor: pointer;
}
</style>

<script>
import {
  Chart as ChartJS,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

import { Bar, Pie } from "vue-chartjs";
import Card from "../Card.vue";
import LoadingButton from "../LoadingButton.vue";
import axios from "axios";
import _ from "lodash";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
);

export default {
  name: "Dashboard",
  components: {
    LoadingButton,
    Bar,
    Pie,
    Card,
  },
  async mounted() {
    await this.fetchConsultores();
  },
  data() {
    return {
      users: [],
      selectedUsers: [],
      relatorios: [],
      showBarChart: false,
      showPizzaChart: false,
      isLoading: false,
      chart: this.getClearCharObject(),
      pizza: this.getClearPizzaChartObject(),
    };
  },
  methods: {
    async fetchConsultores() {
      try {
        const response = await axios.get("/api/consultores");

        this.users = response.data;
      } catch (ex) {
        console.error(ex);
      }
    },
    async fetchRelatorios() {
      try {
        this.showBarChart = this.showPizzaChart = false;

        const selected = this.selectedUsers.map(
          (selectedUser) => selectedUser.co_usuario
        );

        if (selected.length <= 0) {
          return false;
        }

        this.isLoading = true;

        const response = await axios.get(
            `/api/relatorio/?consultores=${selected.join(",")}`
        );

        this.isLoading = false;

        this.relatorios = _.groupBy(response.data, "no_usuario");

        setTimeout(() => this.scrollToSection('#relatorioSection'), 500);
      } catch (ex) {
        console.error(ex);
      }
    },
    async createBarChart() {
      try {
        this.relatorios = [];

        this.showPizzaChart = false;

        const selected = this.selectedUsers.map(
          (selectedUser) => selectedUser.co_usuario
        );

        if (selected.length <= 0) {
          return false;
        }

        this.isLoading = true;

        const response = await axios.get(
          `/api/barChart/?consultores=${selected.join(",")}`
        );

        this.isLoading = false;

        let dates = _.uniq(response.data.map((relatorio) => relatorio.date_emissao))
          .map(date => new Date(date))
          .sort((a, b) => a - b)
          .map(date => `${date.toLocaleString("en-GB", {month: "long",})} ${date.getFullYear()}`);

        let consultores = _.groupBy(response.data, "no_usuario");

        const chart = this.getClearCharObject();

        for (let name in consultores) {
          const dataset = {
            label: name,
            backgroundColor: "#" + ((Math.random() * 0xffffff) << 0).toString(16),
            data: [],
          };

          let consultor = consultores[name];

          for (let i in dates) {
            let found = consultor.find(
              (item) => item.date_emissao === dates[i]
            );

            if (found) {
              dataset.data.push(found.receita_liquida);
            } else {
              dataset.data.push(0);
            }
          }

          chart.data.datasets.push(dataset);
        }

        chart.data.labels = dates;

        this.chart = chart;

        this.showBarChart = true;

        setTimeout(() => this.scrollToSection('#chartSection'), 500);
      } catch (ex) {
        console.error(ex);
      }
    },
    async createPizzaChart() {
      this.relatorios = [];

      this.showBarChart = false;

      const selected = this.selectedUsers.map(
        (selectedUser) => selectedUser.co_usuario
      );

      if (selected.length <= 0) {
        return false;
      }

      this.isLoading = true;

      const { data } = await axios.get(
        `api/pizzaChart/?consultores=${selected.join(",")}`
      );

      this.isLoading = false;

      const pizza = this.getClearPizzaChartObject();

      const dataset = {
        backgroundColor: [],
        data: [],
      };

      Object.keys(data).forEach((key) => {
        pizza.data.labels.push(key);

        dataset.backgroundColor.push(
          "#" + ((Math.random() * 0xffffff) << 0).toString(16)
        );

        dataset.data.push(data[key]);
      });

      pizza.data.datasets.push(dataset);

      this.pizza = pizza;

      this.showPizzaChart = true;

      setTimeout(() => this.scrollToSection('#chartSection'), 500);
    },
    selectUser(user, index) {
      this.selectedUsers.push(user);

      this.users.splice(index, 1);
    },
    unselectUser(selectedUser, index) {
      this.users.push(selectedUser);

      this.selectedUsers.splice(index, 1);
    },
    getClearCharObject() {
      return {
        data: {
          title: "Performance Comercial",
          labels: [],
          datasets: [],
        },
        options: {
          responsive: true,
        },
      };
    },
    getClearPizzaChartObject() {
      return {
        data: {
          labels: [],
          datasets: [],
        },
      };
    },
    scrollToSection(sectionId) {
      const targetSection = document.querySelector(sectionId);

      if (targetSection) {
        const offsetTop = targetSection.offsetTop;

        window.scrollTo({
          top: offsetTop,
          behavior: "smooth"
        });
      }
    }
  },
};
</script>
