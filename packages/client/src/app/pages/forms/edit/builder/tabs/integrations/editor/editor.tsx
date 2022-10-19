import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useParams } from 'react-router-dom';
import { Lightswitch } from '@ff-client/app/components/form-controls/controls/lightswitch';
import { Space } from '@ff-client/app/components/layout/blocks/space';

import {
  selectIntegration,
  toggleIntegration,
} from '../../../../store/slices/integrations';

import { Setting } from './setting/setting';
import { EditorWrapper, SettingsWrapper } from './editor.styles';
import { EmptyEditor } from './empty-editor';

type UrlParams = {
  id: string;
  formId: string;
};

export const Editor: React.FC = () => {
  const { id: integrationId } = useParams<UrlParams>();
  const dispatch = useDispatch();

  const integration = useSelector(selectIntegration(Number(integrationId)));
  if (!integration) {
    return <EmptyEditor />;
  }

  const { id, handle, name, description, enabled, settings } = integration;

  return (
    <EditorWrapper>
      <h1 title={handle}>{name}</h1>
      {!!description && <p>{description}</p>}

      <Lightswitch
        label="Enabled"
        onChange={() => dispatch(toggleIntegration(id))}
        value={enabled}
      />

      <Space />

      <SettingsWrapper>
        {settings.map((setting) => (
          <Setting key={setting.handle} id={id} setting={setting} />
        ))}
      </SettingsWrapper>
    </EditorWrapper>
  );
};
