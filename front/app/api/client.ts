import axios from "axios";

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const requestUrl = error.config?.url || "";
      const isAuthCheck = requestUrl.includes("/users/me");
      
      if (!isAuthCheck && typeof window !== "undefined") {
        const currentPath = window.location.pathname;
        const publicPaths = ["/login", "/register", "/register/pro", "/", "/search"];
        const isPublicPath = publicPaths.some(
          (path) => currentPath === path || currentPath.startsWith("/parking/"),
        );
        
        if (!isPublicPath) {
          window.location.href = "/login";
        }
      }
    }
    return Promise.reject(error);
  },
);

export default apiClient;
