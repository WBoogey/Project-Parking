import apiClient from "@/api/client";
import type { Reservation, Subscription } from "@/types/CustomerTypes";

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
};
