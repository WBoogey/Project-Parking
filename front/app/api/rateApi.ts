import apiClient from "@/api/client";
import type { Rate, CreateRateData } from "@/types/OwnerTypes";

export const rateApi = {
  getRates: async (parkingId: string) => {
    const response = await apiClient.get<{ data: Rate[] }>(
      `/owner/parkings/${parkingId}/rates`,
    );
    return response.data.data;
  },

  createRate: async (parkingId: string, data: CreateRateData) => {
    const response = await apiClient.post(
      `/owner/parkings/${parkingId}/rates`,
      data,
    );
    return response.data;
  },

  updateRate: async (
    parkingId: string,
    rateId: string,
    data: Partial<CreateRateData>,
  ) => {
    const response = await apiClient.put(
      `/owner/parkings/${parkingId}/rates/${rateId}`,
      data,
    );
    return response.data;
  },

  deleteRate: async (parkingId: string, rateId: string) => {
    const response = await apiClient.delete(
      `/owner/parkings/${parkingId}/rates/${rateId}`,
    );
    return response.data;
  },
};
