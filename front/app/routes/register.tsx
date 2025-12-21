import { useNavigate } from "react-router";
import AuthForm from "@/components/organisms/AuthForm";
import { useRegister } from "@/hooks/useAuth";
import type { AuthFormData } from "@/types/AuthFormTypes";

export default function Register() {
  const navigate = useNavigate();
  const { mutate, isPending, error } = useRegister();

  const handleRegister = (data: AuthFormData) => {
    mutate({
      firstName: data.firstName,
      lastName: data.lastName,
      email: data.email,
      password: data.password,
      role: "customer",
    });
  };

  return (
    <div className="flex items-center justify-center bg-gray-50 p-4">
      <AuthForm
        mode="signup"
        onSubmit={handleRegister}
        onNavigateToLogin={() => navigate("/login")}
        onModeChange={(mode) => {
          if (mode === "signup-pro") navigate("/register/pro");
        }}
        onBackToSite={() => navigate("/")}
        isLoading={isPending}
        error={error ? (error as Error).message : undefined}
      />
    </div>
  );
}
