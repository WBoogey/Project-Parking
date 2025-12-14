import { useState } from "react";
import { cn } from "cn-utility";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import type { AuthFormData, AuthFormMode } from "@/types/AuthFormTypes";

interface AuthFormProps {
  mode: AuthFormMode;
  appName?: string;
  onSubmit: (data: AuthFormData) => void;
  onGoogleAuth?: () => void;
  onMicrosoftAuth?: () => void;
  onModeChange?: (mode: AuthFormMode) => void;
  onNavigateToLogin?: () => void;
  onNavigateToSignup?: () => void;
  onBackToSite?: () => void;
  className?: string;
}

const AuthForm = ({
  mode,
  appName = "App Name",
  onSubmit,
  onGoogleAuth,
  onMicrosoftAuth,
  onModeChange,
  onNavigateToLogin,
  onNavigateToSignup,
  onBackToSite,
  className,
}: AuthFormProps) => {
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [companyName, setCompanyName] = useState("");
  const [address, setAddress] = useState("");
  const [city, setCity] = useState("");

  const isSignup = mode === "signup";
  const isLogin = mode === "login";
  const isSignupPro = mode === "signup-pro";
  const showOAuth = !isSignupPro;
  const showNameFields = isSignup || isSignupPro;
  const showCompanyFields = isSignupPro;

  const getTitle = () => {
    if (isLogin) return "Connexion";
    if (isSignupPro) return "Créer un compte professionnel";
    return "Créer un compte";
  };

  const getSubmitLabel = () => {
    if (isLogin) return "Se connecter";
    if (isSignupPro) return "Créer un compte";
    return "Confirmer";
  };

  const isFormValid = () => {
    if (isLogin) {
      return email.trim() !== "" && password.trim() !== "";
    }
    if (isSignup) {
      return firstName.trim() !== "" && lastName.trim() !== "";
    }
    if (isSignupPro) {
      return (
        firstName.trim() !== "" &&
        lastName.trim() !== "" &&
        email.trim() !== "" &&
        password.trim() !== "" &&
        companyName.trim() !== ""
      );
    }
    return false;
  };

  const handleSubmit = () => {
    onSubmit({
      firstName: firstName || undefined,
      lastName: lastName || undefined,
      email,
      password,
      companyName: companyName || undefined,
      address: address || undefined,
      city: city || undefined,
    });
  };

  return (
    <div
      className={cn(
        "w-full max-w-lg bg-primary rounded-3xl p-8 flex flex-col gap-6",
        className,
      )}
    >
      <div className="text-center">
        <p className="text-secondary">{appName}</p>
        <h1 className="text-secondary text-2xl font-bold mt-2">{getTitle()}</h1>
      </div>

      {showOAuth && (
        <>
          <div className="flex gap-4">
            <button
              type="button"
              onClick={onGoogleAuth}
              className="flex-1 py-3 px-4 border border-secondary rounded-full text-secondary text-sm cursor-pointer hover:bg-secondary/5 transition-colors flex items-center justify-center gap-2 whitespace-nowrap"
            >
              <img
                src="/assets/google-icon.png"
                alt="Google"
                className="w-5 h-5"
              />
              Continuer avec Google
            </button>
            <button
              type="button"
              onClick={onMicrosoftAuth}
              className="flex-1 py-3 px-4 border border-secondary rounded-full text-secondary text-sm cursor-pointer hover:bg-secondary/5 transition-colors flex items-center justify-center gap-2 whitespace-nowrap"
            >
              <img
                src="/assets/microsoft-icon.png"
                alt="Microsoft"
                className="w-5 h-5"
              />
              Continuer avec Microsoft
            </button>
          </div>

          <div className="flex items-center gap-4">
            <div className="flex-1 h-px bg-secondary" />
            <span className="text-secondary font-medium">ou</span>
            <div className="flex-1 h-px bg-secondary" />
          </div>
        </>
      )}

      <div className="flex flex-col gap-4">
        {showNameFields && (
          <div className="flex gap-4">
            <InputComplete
              id="firstName"
              label="Prénom*"
              placeholder="Enter Keyword"
              variant="full"
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
            />
            <InputComplete
              id="lastName"
              label="Nom*"
              placeholder="Enter Keyword"
              variant="full"
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
            />
          </div>
        )}

        <InputComplete
          id="email"
          label="Email*"
          placeholder="nom.prenom@email.com"
          type="email"
          variant="full"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />

        <InputComplete
          id="password"
          label="Mot de passe*"
          placeholder="Votre mot de passe"
          variant="full"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />

        {showCompanyFields && (
          <>
            <div className="h-px bg-tertiary my-2" />

            <InputComplete
              id="companyName"
              label="Nom de l'entreprise*"
              placeholder="Société"
              variant="full"
              value={companyName}
              onChange={(e) => setCompanyName(e.target.value)}
            />

            <div className="flex gap-4">
              <InputComplete
                id="address"
                label="Adresse"
                placeholder="Enter Keyword"
                variant="full"
                value={address}
                onChange={(e) => setAddress(e.target.value)}
              />
              <InputComplete
                id="city"
                label="Ville"
                placeholder="Enter Keyword"
                variant="full"
                value={city}
                onChange={(e) => setCity(e.target.value)}
              />
            </div>
          </>
        )}
      </div>

      <div className="flex flex-col items-center gap-2">
        {isSignup && (
          <button
            type="button"
            onClick={() => onModeChange?.("signup-pro")}
            className="text-accent font-medium cursor-pointer hover:underline"
          >
            Créer un compte professionnel
          </button>
        )}

        {isSignupPro && (
          <button
            type="button"
            onClick={() => onModeChange?.("signup")}
            className="text-accent font-medium cursor-pointer hover:underline flex items-center gap-1"
          >
            → Créer un compte particulier
          </button>
        )}

        {(isSignup || isSignupPro) && (
          <p className="text-secondary">
            Déjà un compte ?{" "}
            <button
              type="button"
              onClick={onNavigateToLogin}
              className="text-accent font-medium cursor-pointer hover:underline"
            >
              Se connecter
            </button>
          </p>
        )}

        {isLogin && (
          <p className="text-secondary">
            Vous n&apos;avez pas de compte ?{" "}
            <button
              type="button"
              onClick={onNavigateToSignup}
              className="text-accent font-medium cursor-pointer hover:underline"
            >
              S&apos;inscrire
            </button>
          </p>
        )}
      </div>

      <Button onClick={handleSubmit} size="full" disabled={!isFormValid()}>
        {getSubmitLabel()}
      </Button>

      {onBackToSite && (
        <button
          type="button"
          onClick={onBackToSite}
          className="text-secondary font-medium cursor-pointer hover:underline self-center flex items-center gap-1"
        >
          ← Retour au site
        </button>
      )}
    </div>
  );
};

export default AuthForm;
