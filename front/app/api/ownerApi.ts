import apiClient from "@/api/client";

export interface Parking {
  id: string;
  location: string;
  capacity: number;
  ownerId: string;
}

export const ownerApi = {
  getParkings: async () => {
    const response = await apiClient.get<{ data: Parking[] }>(
      "/owner/parkings",
    );
    return response.data.data;
  },

  addParking: async (data: { location: string; capacity: number }) => {
    const response = await apiClient.post("/owner/parkings", data);
    return response.data;
  },

  deleteParking: async (parkingId: string) => {
    const response = await apiClient.delete("/owner/parkings", {
      data: { parkingId },
    });
    return response.data;
  },
};
