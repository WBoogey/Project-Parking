import axios, { AxiosError } from "axios";

export class ApiError extends Error {
  status: number;
  title: string;
  
  constructor(message: string, status: number, title: string) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.title = title;
  }
}

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
  (error: AxiosError<{ message?: string; title?: string; detail?: string }>) => {
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

    const status = error.response?.status || 500;
    const responseData = error.response?.data;
    const title = responseData?.title || "Erreur";
    const message =
      responseData?.message ||
      responseData?.detail ||
      getErrorMessage(status);

    return Promise.reject(new ApiError(message, status, title));
  },
);

function getErrorMessage(status: number): string {
  switch (status) {
    case 400:
      return "Requête invalide. Vérifiez les données envoyées.";
    case 401:
      return "Vous devez être connecté pour effectuer cette action.";
    case 403:
      return "Vous n'avez pas les droits pour effectuer cette action.";
    case 404:
      return "Ressource non trouvée.";
    case 409:
      return "Conflit avec les données existantes.";
    case 422:
      return "Données invalides.";
    case 500:
      return "Erreur serveur. Veuillez réessayer plus tard.";
    default:
      return "Une erreur est survenue.";
  }
}

export default apiClient;
