import apiClient from "@/api/client";
import type { AuthFormData } from "@/types/AuthFormTypes";

export const authApi = {
  signin: async (data: Pick<AuthFormData, "email" | "password">) => {
    const response = await apiClient.post("/auth/signin", data);
    return response.data;
  },

  signup: async (data: AuthFormData) => {
    const response = await apiClient.post("/auth/signup", data);
    return response.data;
  },

  signout: async () => {
    const response = await apiClient.post("/auth/signout");
    return response.data;
  },
};
