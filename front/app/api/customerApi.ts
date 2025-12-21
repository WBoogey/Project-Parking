import apiClient from "@/api/client";
import type {
  Reservation,
  Subscription,
  Stationing,
} from "@/types/CustomerTypes";

export const customerApi = {
  getReservations: async () => {
    const response = await apiClient.get<{ data: Reservation[] }>(
      "/customer/reservations",
    );
    return response.data.data;
  },

  getSubscriptions: async () => {
    const response = await apiClient.get<{ data: Subscription[] }>(
      "/customer/subscriptions",
    );
    return response.data.data;
  },

  getStationings: async () => {
    const response = await apiClient.get<{ data: Stationing[] }>(
      "/customer/stationings",
    );
    return response.data.data;
  },
};
