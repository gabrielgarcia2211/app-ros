<template>
    <Card>
        <template #title>Property</template>
        <template #content>
            <div class="p-d-flex p-jc-end p-mb-3">
                <Button
                    icon="pi pi-plus"
                    rounded
                    raised
                    @click="addProperty"
                    style="margin-right: 10px"
                />
                <Button
                    icon="pi pi-filter-slash"
                    class="p-button-danger p-button-sm"
                    rounded
                    raised
                    @click="clearFilters"
                />
            </div>
            <DataTable
                v-model:filters="filters"
                :loading="loading"
                :value="users"
                :paginator="true"
                :rows="perPage"
                :sortField="sortField"
                :sortOrder="sortOrder"
                :totalRecords="totalRecords"
                :lazy="true"
                @page="onPage"
                @sort="onSort"
                @filter="onFilters"
                filterDisplay="menu"
                removableSort
                stripedRows
                scrollable
            >
                <!-- Name Column -->
                <Column
                    field="name"
                    header="Name"
                    sortable
                    style="min-width: 150px"
                >
                    <template #body="{ data }">
                        {{ data.name }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by name"
                        />
                    </template>
                </Column>
                <!-- Address Column -->
                <Column
                    field="address"
                    header="Address"
                    sortable
                    style="min-width: 200px"
                >
                    <template #body="{ data }">
                        {{ data.address }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by address"
                        />
                    </template>
                </Column>
                <!-- Status Column -->
                <Column
                    field="status"
                    header="Status"
                    sortable
                    style="min-width: 120px"
                >
                    <template #body="{ data }">
                        {{ data.status }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by status"
                        />
                    </template>
                </Column>
                <!-- Owner Name Column -->
                <Column
                    field="owner_name"
                    header="Owner Name"
                    sortable
                    style="min-width: 150px"
                >
                    <template #body="{ data }">
                        {{ data.owner_name }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by owner name"
                        />
                    </template>
                </Column>
                <!-- Tenant Name Column -->
                <Column
                    field="tenant_name"
                    header="Tenant Name"
                    sortable
                    style="min-width: 150px"
                >
                    <template #body="{ data }">
                        {{ data.tenant_name }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by tenant name"
                        />
                    </template>
                </Column>
                <!-- Property Type Name Column -->
                <Column
                    field="property_type_name"
                    header="Property Type"
                    sortable
                    style="min-width: 180px"
                >
                    <template #body="{ data }">
                        {{ data.property_type_name }}
                    </template>
                    <template #filter="{ filterModel }">
                        <InputText
                            v-model="filterModel.value"
                            type="text"
                            class="p-column-filter"
                            placeholder="Search by property type"
                        />
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
    <!-- gestion de aliados -->
    <ManagemenPropertyComponent
        v-if="dialogVisible"
        :dialogVisible="dialogVisible"
        :selectedProperty="selectedProperty"
        @hidden="hidden"
        @reload="reload"
    />
</template>

<script>
// Importar Librerias o Modulos

import Card from "primevue/card";
import { FilterMatchMode, FilterOperator } from "@primevue/core/api";
import ManagemenPropertyComponent from "./management/ManagemenPropertyComponent.vue";
import InputText from 'primevue/inputtext';
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";

export default {
    props: [],
    data() {
        return {
            users: [],
            perPage: 5,
            totalRecords: 0,
            page: 1,
            sortField: "",
            sortOrder: null,
            filters: null,
            filtroInfo: [],
            loading: true,
            //
            selectedProperty: null,
            dialogVisible: false,
        };
    },
    components: {
        FilterMatchMode,
        FilterOperator,
        Card,
        ManagemenPropertyComponent,
        DataTable,
        Column,
        Button,
        InputText
    },
    created() {
        this.initFilters();
    },
    mounted() {
        this.fetchProperty();
    },
    methods: {
        initFilters() {
            this.filters = {
                name: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                address: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                status: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                owner_name: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                tenant_name: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                property_type_name: {
                    clear: false,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
            };
        },
        clearFilters() {
            this.initFilters();
            this.filtroInfo = [];
            this.fetchProperty();
        },
        onPage(event) {
            this.page = event.page + 1;
            this.perPage = event.rows;
            this.fetchProperty();
        },
        onSort(event) {
            this.page = 1;
            this.sortField = event.sortField;
            this.sortOrder = event.sortOrder;
            this.fetchProperty();
        },
        onFilters(event) {
            this.page = 1;
            this.filtroInfo = [];
            for (const [key, filter] of Object.entries(event.filters)) {
                if (filter.constraints) {
                    for (const constraint of filter.constraints) {
                        if (constraint.value) {
                            this.filtroInfo.push([
                                this.$relationTableProperty(key),
                                constraint.matchMode,
                                constraint.value,
                            ]);
                        }
                    }
                }
            }
            this.fetchProperty();
        },
        fetchProperty() {
            this.loading = true;
            this.$axios
                .get("/properties/list", {
                    params: {
                        page: this.page,
                        perPage: this.perPage,
                        sort: [this.sortField, this.sortOrder],
                        filters: this.filtroInfo,
                        select: this.filterSelect ?? null,
                    },
                })
                .then((response) => {
                    this.users = response.data.data;
                    this.totalRecords = response.data.total;
                    this.loading = false;
                })
                .catch((error) => {
                    this.$readStatusHttp(error);
                    this.loading = false;
                });
        },
        addProperty() {
            this.selectedProperty = null;
            this.dialogVisible = true;
        },
        editAliado(aliado) {
            this.selectedProperty = aliado;
            this.dialogVisible = true;
        },
        async deleteAliado(aliadoId) {
            const result = await this.$swal.fire({
                title: "Are you sure?",
                text: "You are about to delete this ally. Are you sure you want to continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancelar",
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/properties/${aliadoId}`);
                    this.$alertSuccess("Aliado eliminado con éxito");
                    this.fetchProperty();
                } catch (error) {
                    this.$readStatusHttp(error);
                }
            }
        },
        reload() {
            this.fetchProperty();
            this.selectedProperty = null;
            this.dialogVisible = false;
        },
        hidden(status) {
            this.dialogVisible = status;
        }
    },
};
</script>

<style>
.p-d-flex {
    display: flex;
}
.p-jc-end {
    justify-content: flex-end;
}
.p-mb-3 {
    margin-bottom: 1rem;
}
.p-button-sm {
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
}
</style>
