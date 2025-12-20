import apiClient from "@/api/client";

export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
}

export const userApi = {
  getMe: async () => {
    const response = await apiClient.get<{ data: User }>("/users/me");
    return response.data.data;
  },
};
