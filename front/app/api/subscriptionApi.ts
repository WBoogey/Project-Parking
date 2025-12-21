import apiClient from "@/api/client";
import type {
  SubscriptionDetail,
  CreateSubscriptionData,
} from "@/types/SubscriptionTypes";

export const subscriptionApi = {
  getSubscriptions: async () => {
    const response = await apiClient.get<{ data: SubscriptionDetail[] }>(
      "/subscriptions",
    );
    return response.data.data;
  },

  getSubscription: async (id: string) => {
    const response = await apiClient.get<{ data: SubscriptionDetail }>(
      `/subscriptions/${id}`,
    );
    return response.data.data;
  },

  createSubscription: async (data: CreateSubscriptionData) => {
    const response = await apiClient.post("/subscriptions", data);
    return response.data;
  },

  updateSubscription: async (
    id: string,
    data: Partial<CreateSubscriptionData>,
  ) => {
    const response = await apiClient.put(`/subscriptions/${id}`, data);
    return response.data;
  },

  cancelSubscription: async (id: string) => {
    const response = await apiClient.delete(`/subscriptions/${id}`);
    return response.data;
  },
};
